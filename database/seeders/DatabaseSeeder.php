<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Crée le compte administrateur initial.
     *
     * Le mot de passe vient de ADMIN_PASSWORD (.env). S'il est absent, un mot de
     * passe aléatoire est généré et affiché une seule fois dans la console.
     */
    public function run(): void
    {
        $email = config('app.admin_email');

        if (! User::where('email', $email)->exists()) {
            $password = config('app.admin_password') ?: Str::password(16);

            $admin = new User([
                'name' => 'Admin Le Printemps',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $admin->assignRole(User::ROLE_ADMIN);
            $admin->email_verified_at = now();
            $admin->save();

            if (! config('app.admin_password')) {
                $this->command?->warn("Administrateur créé : {$email} / {$password} (changez-le après connexion)");
            }
        }

        // Client de test uniquement en local
        if (app()->environment('local')) {
            User::firstOrCreate(
                ['email' => 'client@test.com'],
                ['name' => 'Client Test', 'password' => Hash::make('password')]
            );
        }
    }
}
