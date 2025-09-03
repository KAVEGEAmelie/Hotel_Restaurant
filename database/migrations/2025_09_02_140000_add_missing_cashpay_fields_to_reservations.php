<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (!Schema::hasColumn('reservations', 'cashpay_ledger_reference')) {
                $table->string('cashpay_ledger_reference')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'montant_paye')) {
                $table->decimal('montant_paye', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'methode_paiement')) {
                $table->string('methode_paiement')->nullable();
            }
            
            if (!Schema::hasColumn('reservations', 'date_paiement')) {
                $table->timestamp('date_paiement')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'cashpay_ledger_reference',
                'montant_paye',
                'methode_paiement',
                'date_paiement'
            ]);
        });
    }
};
