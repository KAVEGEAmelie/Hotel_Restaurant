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
            $table->unsignedBigInteger('admin_confirme_id')->nullable()->after('statut_paiement');
            $table->timestamp('date_confirmation')->nullable()->after('admin_confirme_id');
            
            $table->foreign('admin_confirme_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['admin_confirme_id']);
            $table->dropColumn(['admin_confirme_id', 'date_confirmation']);
        });
    }
};
