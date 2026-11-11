<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesAdminTest extends TestCase
{
    use RefreshDatabase;

    private function utilisateur(string $role, string $email = null): User
    {
        $user = User::factory()->create(['email' => $email ?? fake()->unique()->safeEmail()]);
        $user->assignRole($role);
        $user->save();

        return $user;
    }

    public function test_un_email_contenant_gerant_ne_donne_aucun_droit(): void
    {
        $this->post('/register', [
            'name' => 'Pirate',
            'email' => 'gerant.pirate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'gerant.pirate@example.com')->firstOrFail();
        $this->assertFalse($user->canAccessAdmin());

        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect('/');
    }

    public function test_l_inscription_ne_permet_pas_de_choisir_son_role(): void
    {
        $this->post('/register', [
            'name' => 'Pirate',
            'email' => 'pirate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'is_admin' => 1,
        ]);

        $this->assertSame('client', User::where('email', 'pirate@example.com')->first()->fresh()->role);
    }

    public function test_un_client_n_accede_pas_a_l_admin(): void
    {
        $this->actingAs($this->utilisateur(User::ROLE_CLIENT))
            ->get('/admin/dashboard')->assertRedirect('/');
    }

    public function test_un_gerant_accede_a_l_admin_mais_pas_aux_utilisateurs(): void
    {
        $gerant = $this->utilisateur(User::ROLE_GERANT);

        $this->actingAs($gerant)->get('/admin/dashboard')->assertOk();
        $this->actingAs($gerant)->get('/admin/utilisateurs')->assertRedirect();
    }

    public function test_un_admin_accede_aux_utilisateurs(): void
    {
        $this->actingAs($this->utilisateur(User::ROLE_ADMIN))
            ->get('/admin/utilisateurs')->assertOk();
    }
}
