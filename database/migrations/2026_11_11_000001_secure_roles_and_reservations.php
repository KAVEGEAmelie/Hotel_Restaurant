<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * - Les rôles deviennent la seule source de vérité (colonne users.role).
 * - Chaque réservation reçoit un jeton d'accès secret pour les URL publiques.
 * - Les statuts de réservation sont harmonisés (en_attente, confirmee, annulee, terminee).
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- 1. Rôles utilisateurs ---------------------------------------------
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

            // Les gérants étaient auparavant détectés par leur email ("gerant").
            // On conserve leur accès ; vérifiez ensuite la liste dans l'admin.
            DB::table('users')
                ->where('is_admin', false)
                ->where('role', 'client')
                ->where('email', 'like', '%gerant%')
                ->update(['role' => 'gerant']);
        }

        // --- 2. Jeton d'accès aux réservations ---------------------------------
        if (! Schema::hasColumn('reservations', 'access_token')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('access_token', 64)->nullable()->unique();
            });
        }

        DB::table('reservations')->whereNull('access_token')->orderBy('id')
            ->each(function ($reservation) {
                DB::table('reservations')->where('id', $reservation->id)
                    ->update(['access_token' => Str::random(48)]);
            });

        // --- 3. Harmonisation des statuts ---------------------------------------
        $mapping = [
            'pending' => 'en_attente',
            'confirmée' => 'confirmee',
            'confirmed' => 'confirmee',
            'annulée' => 'annulee',
            'cancelled' => 'annulee',
        ];

        foreach ($mapping as $ancien => $nouveau) {
            DB::table('reservations')->where('statut', $ancien)->update(['statut' => $nouveau]);
        }

        // "payée" était un statut de réservation : c'est en réalité un statut de paiement.
        DB::table('reservations')->where('statut', 'payée')
            ->update(['statut' => 'confirmee', 'statut_paiement' => 'paye']);

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('statut')->default('en_attente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropUnique(['access_token']);
            $table->dropColumn('access_token');
        });
    }
};
