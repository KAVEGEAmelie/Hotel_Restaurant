<?php

namespace Tests\Feature;

use App\Models\Chambre;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function chambre(array $attributs = []): Chambre
    {
        return Chambre::create(array_merge([
            'nom' => 'Suite Test',
            'slug' => 'suite-test-'.uniqid(),
            'description_courte' => 'Courte',
            'description_longue' => 'Longue',
            'image_principale' => 'chambres/test.jpg',
            'prix_par_nuit' => 25000,
            'capacite' => 2,
            'est_disponible' => true,
        ], $attributs));
    }

    private function donnees(Chambre $chambre, string $arrivee, string $depart): array
    {
        return [
            'chambre_id' => $chambre->id,
            'client_nom' => 'Doe',
            'client_prenom' => 'Jane',
            'client_email' => 'jane@example.com',
            'client_telephone' => '+22890000000',
            'check_in_date' => $arrivee,
            'check_out_date' => $depart,
            'nombre_invites' => 2,
        ];
    }

    public function test_une_reservation_recoit_un_jeton_et_redirige_avec_lui(): void
    {
        $chambre = $this->chambre();

        $response = $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));

        $reservation = Reservation::firstOrFail();
        $this->assertSame(48, strlen($reservation->access_token));
        $this->assertSame(Reservation::STATUT_EN_ATTENTE, $reservation->statut);
        $this->assertEquals(50000, $reservation->prix_total);
        $response->assertRedirect(route('reservations.confirm', $reservation));
        $this->assertStringContainsString($reservation->access_token, $response->headers->get('Location'));
    }

    public function test_les_urls_publiques_refusent_l_identifiant_numerique(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        $reservation = Reservation::firstOrFail();

        $this->get("/reservations/{$reservation->id}/confirm")->assertNotFound();
        $this->get("/payment/receipt/{$reservation->id}")->assertNotFound();
        $this->get("/reservations/{$reservation->access_token}/confirm")->assertOk();
    }

    public function test_pas_de_double_reservation_meme_si_confirmee_par_l_admin(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(4)->toDateString()));
        Reservation::first()->update(['statut' => Reservation::STATUT_CONFIRMEE]);

        $this->post('/reservations', $this->donnees($chambre, now()->addDays(2)->toDateString(), now()->addDays(3)->toDateString()))
            ->assertSessionHas('error');

        $this->assertSame(1, Reservation::count());
    }

    public function test_reserver_le_jour_du_depart_d_un_autre_client_est_autorise(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(3)->toDateString(), now()->addDays(5)->toDateString()));

        $this->assertSame(2, Reservation::count());
    }

    public function test_une_reservation_annulee_libere_la_chambre(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        Reservation::first()->update(['statut' => Reservation::STATUT_ANNULEE]);

        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));

        $this->assertSame(2, Reservation::count());
    }

    public function test_le_webhook_accepte_un_appel_sans_jeton_csrf_et_confirme_le_paiement(): void
    {
        Mail::fake();
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        $reservation = Reservation::firstOrFail();
        $reservation->update(['transaction_id' => 'HP_TEST_1']);

        Http::fake([
            '*/payment/check' => Http::response([
                'code' => '00',
                'message' => 'SUCCES',
                'data' => ['amount' => '50000', 'currency' => 'XOF', 'payment_method' => 'FLOOZ', 'status' => 'ACCEPTED'],
            ]),
        ]);

        // Pas de withoutMiddleware : on vérifie bien que le CSRF ne bloque pas CinetPay.
        $this->post('/payment/webhook', ['cpm_trans_id' => 'HP_TEST_1', 'cpm_site_id' => '123456'])->assertOk();
        // Une deuxième notification ne renvoie pas d'email.
        $this->post('/payment/webhook', ['cpm_trans_id' => 'HP_TEST_1', 'cpm_site_id' => '123456'])->assertOk();

        $reservation->refresh();
        $this->assertSame(Reservation::PAIEMENT_PAYE, $reservation->statut_paiement);
        $this->assertSame(Reservation::STATUT_CONFIRMEE, $reservation->statut);
        Mail::assertSentCount(1);
        Mail::assertSent(\App\Mail\ReservationConfirmationMail::class, fn ($mail) => $mail->hasTo('jane@example.com'));
    }

    public function test_un_montant_insuffisant_n_est_pas_confirme(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        $reservation = Reservation::firstOrFail();
        $reservation->update(['transaction_id' => 'HP_TEST_2']);

        Http::fake(['*/payment/check' => Http::response(['code' => '00', 'data' => ['amount' => '100', 'currency' => 'XOF']])]);

        $this->post('/payment/webhook', ['cpm_trans_id' => 'HP_TEST_2'])->assertOk();

        $this->assertNotSame(Reservation::PAIEMENT_PAYE, $reservation->fresh()->statut_paiement);
    }

    public function test_un_paiement_en_attente_n_est_pas_marque_en_echec(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        $reservation = Reservation::firstOrFail();
        $reservation->update(['transaction_id' => 'HP_TEST_3']);

        Http::fake(['*/payment/check' => Http::response(['code' => '662', 'message' => 'WAITING_CUSTOMER_PAYMENT'])]);

        $this->get(route('payment.return', $reservation))->assertOk()->assertViewIs('payment.pending');
        $this->assertSame(Reservation::PAIEMENT_EN_ATTENTE, $reservation->fresh()->statut_paiement);
    }

    public function test_les_reservations_non_payees_expirent(): void
    {
        $chambre = $this->chambre();
        $this->post('/reservations', $this->donnees($chambre, now()->addDays(1)->toDateString(), now()->addDays(3)->toDateString()));
        Reservation::query()->update(['created_at' => now()->subHours(3)]);

        $this->artisan('reservations:expirer')->assertSuccessful();

        $this->assertSame(Reservation::STATUT_ANNULEE, Reservation::first()->statut);
    }
}
