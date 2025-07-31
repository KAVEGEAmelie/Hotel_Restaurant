<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Chambre;
use App\Services\CashPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CashPayIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $chambre;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer une chambre de test
        $this->chambre = Chambre::factory()->create([
            'nom' => 'Chambre Test',
            'prix' => 50000,
        ]);
    }

    /** @test */
    public function it_can_show_payment_page_for_valid_reservation()
    {
        $reservation = Reservation::factory()->create([
            'chambre_id' => $this->chambre->id,
            'statut' => 'pending',
            'prix_total' => 50000,
        ]);

        $response = $this->get(route('payment.show', $reservation));

        $response->assertStatus(200);
        $response->assertViewIs('payment.show');
        $response->assertViewHas('reservation');
    }

    /** @test */
    public function it_redirects_invalid_reservation_status()
    {
        $reservation = Reservation::factory()->create([
            'chambre_id' => $this->chambre->id,
            'statut' => 'confirmée',
        ]);

        $response = $this->get(route('payment.show', $reservation));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_validates_payment_method()
    {
        $reservation = Reservation::factory()->create([
            'chambre_id' => $this->chambre->id,
            'statut' => 'pending',
        ]);

        $response = $this->post(route('payment.process', $reservation), [
            'payment_method' => 'invalid_method'
        ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    /** @test */
    public function it_handles_cashpay_service_errors()
    {
        // Mock le service CashPay pour simuler une erreur
        $this->mock(CashPayService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('preparePaymentData')->andReturn([]);
            $mock->shouldReceive('initializePayment')->andReturn([
                'success' => false,
                'error' => 'API Error'
            ]);
        });

        $reservation = Reservation::factory()->create([
            'chambre_id' => $this->chambre->id,
            'statut' => 'pending',
        ]);

        $response = $this->post(route('payment.process', $reservation), [
            'payment_method' => 'card'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_handles_callback_with_valid_jwt()
    {
        // Mock le service CashPay pour simuler un token JWT valide
        $this->mock(CashPayService::class, function ($mock) {
            $mock->shouldReceive('decodeCallbackToken')->andReturn([
                'success' => true,
                'data' => [
                    'order_reference' => 'CP-TEST-123',
                    'status' => 'SUCCESS',
                    'amount' => 50000
                ]
            ]);
        });

        $reservation = Reservation::factory()->create([
            'transaction_ref' => 'CP-TEST-123',
            'statut' => 'pending',
        ]);

        $response = $this->post(route('payment.callback'), [
            'data' => 'fake_jwt_token'
        ]);

        $response->assertStatus(200);
        $response->assertSee('OK');

        // Vérifier que le statut a été mis à jour
        $reservation->refresh();
        $this->assertEquals('confirmée', $reservation->statut);
    }

    /** @test */
    public function it_handles_callback_with_invalid_jwt()
    {
        // Mock le service CashPay pour simuler un token JWT invalide
        $this->mock(CashPayService::class, function ($mock) {
            $mock->shouldReceive('decodeCallbackToken')->andReturn([
                'success' => false,
                'error' => 'Invalid JWT'
            ]);
        });

        $response = $this->post(route('payment.callback'), [
            'data' => 'invalid_jwt_token'
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function it_handles_callback_without_data()
    {
        $response = $this->post(route('payment.callback'));

        $response->assertStatus(400);
    }

    /** @test */
    public function it_handles_return_url()
    {
        $response = $this->get(route('payment.return'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
    }
} 