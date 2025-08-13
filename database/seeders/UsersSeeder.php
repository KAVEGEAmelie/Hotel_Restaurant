<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Supprimer les utilisateurs existants
        User::truncate();

        // Administrateur principal
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'phone' => '90000001',
            'email_verified_at' => now(),
        ]);

        // Votre compte personnel
        User::create([
            'name' => 'KA.A',
            'email' => 'camillekvg99@gmail.com',
            'password' => Hash::make('Admin@2024'),
            'is_admin' => true,
            'phone' => '99999999',
            'email_verified_at' => now(),
        ]);

        // Gérant
        User::create([
            'name' => 'Gérant Hotel',
            'email' => 'gerant@hotel.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '90000002',
            'email_verified_at' => now(),
        ]);

        // Client de test
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'client@test.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '90000003',
            'email_verified_at' => now(),
        ]);

        echo "✅ Utilisateurs créés :\n";
        echo "  Admin : admin@hotel.com / password\n";
        echo "  Votre compte : camillekvg99@gmail.com / Admin@2024\n";
        echo "  Gérant : gerant@hotel.com / password\n";
        echo "  Client : client@test.com / password\n";
    }
}
