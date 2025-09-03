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
            // Supprimer les anciens champs CashPay V1
            $table->dropIndex(['cashpay_invoice_id']);
            $table->dropIndex(['cashpay_status']);
            $table->dropColumn([
                'cashpay_invoice_id',
                'cashpay_payment_link',
                'cashpay_response_data',
                'cashpay_webhook_data',
                'cashpay_created_at',
                'cashpay_updated_at'
            ]);
            
            // Ajouter les nouveaux champs CashPay V2.0 selon la documentation
            $table->string('cashpay_order_reference')->nullable()->after('cashpay_status');
            $table->string('cashpay_merchant_reference')->nullable()->after('cashpay_order_reference');
            $table->text('cashpay_bill_url')->nullable()->after('cashpay_merchant_reference');
            $table->string('cashpay_code')->nullable()->after('cashpay_bill_url');
            $table->text('cashpay_qrcode_url')->nullable()->after('cashpay_code');
            $table->text('cashpay_data')->nullable()->after('cashpay_qrcode_url');
            $table->text('cashpay_webhook_data')->nullable()->after('cashpay_data');
            
            // Index pour optimiser les recherches V2.0
            $table->index('cashpay_order_reference');
            $table->index('cashpay_merchant_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les champs CashPay V2.0
            $table->dropIndex(['cashpay_order_reference']);
            $table->dropIndex(['cashpay_merchant_reference']);
            $table->dropColumn([
                'cashpay_order_reference',
                'cashpay_merchant_reference',
                'cashpay_bill_url',
                'cashpay_code',
                'cashpay_qrcode_url',
                'cashpay_data',
                'cashpay_webhook_data'
            ]);
            
            // Rétablir les anciens champs CashPay V1
            $table->string('cashpay_invoice_id')->nullable()->after('notes_paiement');
            $table->text('cashpay_payment_link')->nullable()->after('cashpay_invoice_id');
            $table->json('cashpay_response_data')->nullable()->after('cashpay_status');
            $table->json('cashpay_webhook_data')->nullable()->after('cashpay_response_data');
            $table->timestamp('cashpay_created_at')->nullable()->after('cashpay_webhook_data');
            $table->timestamp('cashpay_updated_at')->nullable()->after('cashpay_created_at');
            
            // Index V1
            $table->index('cashpay_invoice_id');
            $table->index('cashpay_status');
        });
    }
};
