<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // === 1. Création de l'utilisateur ADMINISTRATEUR ===
        // On utilise firstOrCreate pour éviter les erreurs si l'admin existe déjà.
        User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin Le Printemps',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // === 2. (Optionnel) Création d'un utilisateur CLIENT de test ===
        User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('password'),
            ]
        );
    }
}
