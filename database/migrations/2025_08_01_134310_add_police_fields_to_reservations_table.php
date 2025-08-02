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
        $table->date('client_date_naissance')->nullable()->after('client_telephone');
        $table->string('client_nationalite')->nullable()->after('client_date_naissance');
        $table->string('client_profession')->nullable()->after('client_nationalite');
        $table->string('client_domicile')->nullable()->after('client_profession');
        $table->string('motif_voyage')->nullable()->after('client_domicile');
        $table->string('venant_de')->nullable()->after('motif_voyage');
        $table->string('allant_a')->nullable()->after('venant_de');
        $table->string('piece_identite_numero')->nullable()->after('allant_a');
        $table->date('piece_identite_delivree_le')->nullable()->after('piece_identite_numero');
        $table->string('piece_identite_delivree_a')->nullable()->after('piece_identite_delivree_le');
        $table->string('personne_a_prevenir')->nullable()->after('piece_identite_delivree_a');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
        });
    }
};
