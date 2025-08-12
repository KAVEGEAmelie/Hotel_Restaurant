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
            $table->string('transaction_id')->nullable()->after('statut');
            $table->text('payment_url')->nullable()->after('transaction_id');
            $table->string('payment_token')->nullable()->after('payment_url');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->after('payment_token');
            $table->timestamp('payment_date')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_date');
            $table->string('operator_id')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'payment_url', 
                'payment_token',
                'payment_status',
                'payment_date',
                'payment_method',
                'operator_id'
            ]);
        });
    }
};
