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
            // Champs de paiement manquants
            $table->string('statut_paiement')->default('en_attente')->after('statut');
            $table->decimal('montant_paye', 10, 2)->nullable()->after('statut_paiement');
            $table->string('methode_paiement')->nullable()->after('montant_paye');
            $table->timestamp('date_paiement')->nullable()->after('methode_paiement');
            $table->text('notes_paiement')->nullable()->after('date_paiement');
            $table->json('cinetpay_payment_data')->nullable()->after('notes_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'statut_paiement',
                'montant_paye',
                'methode_paiement',
                'date_paiement',
                'notes_paiement',
                'cinetpay_payment_data'
            ]);
        });
    }
};
