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
            // Supprimer les colonnes liées à CinetPay qui existent
            if (Schema::hasColumn('reservations', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('reservations', 'payment_url')) {
                $table->dropColumn('payment_url');
            }
            if (Schema::hasColumn('reservations', 'payment_token')) {
                $table->dropColumn('payment_token');
            }
            if (Schema::hasColumn('reservations', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('reservations', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
            if (Schema::hasColumn('reservations', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('reservations', 'operator_id')) {
                $table->dropColumn('operator_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Recréer les colonnes si besoin (optionnel)
            $table->string('transaction_id')->nullable();
            $table->text('payment_url')->nullable();
            $table->string('payment_token')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('operator_id')->nullable();
            $table->json('cinetpay_data')->nullable();
            $table->json('cinetpay_payment_data')->nullable();
        });
    }
};
