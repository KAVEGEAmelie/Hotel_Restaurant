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
            // Champs pour l'intégration CashPay
            $table->string('cashpay_invoice_id')->nullable()->after('notes_paiement');
            $table->text('cashpay_payment_link')->nullable()->after('cashpay_invoice_id');
            $table->string('cashpay_status')->nullable()->after('cashpay_payment_link');
            $table->json('cashpay_response_data')->nullable()->after('cashpay_status');
            $table->json('cashpay_webhook_data')->nullable()->after('cashpay_response_data');
            $table->timestamp('cashpay_created_at')->nullable()->after('cashpay_webhook_data');
            $table->timestamp('cashpay_updated_at')->nullable()->after('cashpay_created_at');
            
            // Index pour optimiser les recherches
            $table->index('cashpay_invoice_id');
            $table->index('cashpay_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les champs CashPay
            $table->dropIndex(['cashpay_invoice_id']);
            $table->dropIndex(['cashpay_status']);
            $table->dropColumn([
                'cashpay_invoice_id',
                'cashpay_payment_link',
                'cashpay_status',
                'cashpay_response_data',
                'cashpay_webhook_data',
                'cashpay_created_at',
                'cashpay_updated_at'
            ]);
        });
    }
};
