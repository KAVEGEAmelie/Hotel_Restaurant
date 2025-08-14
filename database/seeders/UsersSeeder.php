<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Créer ou mettre à jour les comptes admin sans truncate (plus sûr pour la production)
        
        // Administrateur principal
        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'phone' => '90000001',
                'email_verified_at' => now(),
            ]
        );

        // Votre compte personnel
        User::updateOrCreate(
            ['email' => 'camillekvg99@gmail.com'],
            [
                'name' => 'KA.A',
                'password' => Hash::make('Admin@2024'),
                'is_admin' => true,
                'phone' => '99999999',
                'email_verified_at' => now(),
            ]
        );

        // Gérant (accès partiel - PAS de gestion utilisateurs)
        User::updateOrCreate(
            ['email' => 'gerant@hotel.com'],
            [
                'name' => 'Gérant Hotel',
                'password' => Hash::make('password'),
                'is_admin' => false, // Pas admin complet
                'phone' => '90000002',
                'email_verified_at' => now(),
            ]
        );

        // Client de test
        User::updateOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Jean Dupont',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'phone' => '90000003',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Utilisateurs créés :\n";
        echo "  Admin : admin@hotel.com / password\n";
        echo "  Votre compte : camillekvg99@gmail.com / Admin@2024\n";
        echo "  Gérant : gerant@hotel.com / password\n";
        echo "  Client : client@test.com / password\n";
    }
}
