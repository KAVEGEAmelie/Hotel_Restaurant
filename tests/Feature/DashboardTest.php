<?php

namespace Tests\Feature;

use App\Models\Chambre;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function utilisateur(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->save();

        return $user;
    }

    private function chambre(): Chambre
    {
        return Chambre::create([
            'nom' => 'Chambre '.uniqid(),
            'slug' => uniqid(),
            'description_courte' => 'c',
            'description_longue' => 'l',
            'image_principale' => 'x.jpg',
            'prix_par_nuit' => 20000,
            'capacite' => 2,
        ]);
    }

    private function reservation(Chambre $chambre, array $attributs): Reservation
    {
        return Reservation::create(array_merge([
            'chambre_id' => $chambre->id,
            'client_nom' => 'Nom',
            'client_prenom' => 'Prenom',
            'client_email' => 'c@example.com',
            'client_telephone' => '90000000',
            'check_in_date' => today()->addDays(10),
            'check_out_date' => today()->addDays(12),
            'nombre_invites' => 1,
            'prix_total' => 40000,
            'statut' => Reservation::STATUT_CONFIRMEE,
            'statut_paiement' => Reservation::PAIEMENT_PAYE,
        ], $attributs));
    }

    public function test_les_indicateurs_sont_calcules_correctement(): void
    {
        [$c1, $c2, $c3, $c4] = [$this->chambre(), $this->chambre(), $this->chambre(), $this->chambre()];

        // Occupée ce soir (arrivée hier) + payée ce mois
        $this->reservation($c1, ['check_in_date' => today()->subDay(), 'check_out_date' => today()->addDay(),
            'montant_paye' => 40000, 'date_paiement' => now()]);
        // Arrivée aujourd'hui (occupée ce soir aussi)
        $this->reservation($c2, ['client_nom' => 'Arrivant', 'check_in_date' => today(), 'check_out_date' => today()->addDays(2),
            'montant_paye' => 10000, 'date_paiement' => now()]);
        // Départ aujourd'hui : n'occupe PAS la chambre ce soir. Payée le mois dernier.
        $this->reservation($c3, ['client_nom' => 'Partant', 'check_in_date' => today()->subDays(2), 'check_out_date' => today(),
            'montant_paye' => 99999, 'date_paiement' => now()->subMonthNoOverflow()->startOfMonth()]);
        // En attente de paiement
        $this->reservation($c4, ['statut' => Reservation::STATUT_EN_ATTENTE, 'statut_paiement' => Reservation::PAIEMENT_EN_ATTENTE]);
        // Payée mais annulée : à rembourser (ne compte pas dans l'occupation)
        $this->reservation($c4, ['client_nom' => 'Arembourser', 'statut' => Reservation::STATUT_ANNULEE,
            'check_in_date' => today(), 'check_out_date' => today()->addDay(), 'montant_paye' => 5000, 'date_paiement' => now()]);

        $response = $this->actingAs($this->utilisateur(User::ROLE_GERANT))->get('/admin/dashboard')->assertOk();

        $stats = $response->viewData('stats');
        $this->assertSame(2, $stats['chambres_occupees']);
        $this->assertSame(4, $stats['total_chambres']);
        $this->assertSame(50, $stats['taux_occupation']);
        $this->assertEquals(55000, $stats['chiffre_affaires_mois']); // 40000 + 10000 + 5000
        $this->assertSame(1, $stats['en_attente_paiement']);

        $this->assertSame(['Arrivant'], $response->viewData('arrivees')->pluck('client_nom')->all());
        $this->assertSame(['Partant'], $response->viewData('departs')->pluck('client_nom')->all());
        $this->assertSame(['Arembourser'], $response->viewData('aRembourser')->pluck('client_nom')->all());
        $response->assertSee('remboursement(s) à effectuer');
    }

    public function test_le_menu_utilisateurs_est_cache_aux_gerants(): void
    {
        $this->actingAs($this->utilisateur(User::ROLE_GERANT))
            ->get('/admin/dashboard')->assertDontSee('Gestion Utilisateurs');

        $this->actingAs($this->utilisateur(User::ROLE_ADMIN))
            ->get('/admin/dashboard')->assertSee('Gestion Utilisateurs');
    }

    public function test_le_tableau_de_bord_vide_s_affiche(): void
    {
        $this->actingAs($this->utilisateur(User::ROLE_ADMIN))
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Aucune arrivée prévue aujourd')
            ->assertSee('0 %');
    }
}
