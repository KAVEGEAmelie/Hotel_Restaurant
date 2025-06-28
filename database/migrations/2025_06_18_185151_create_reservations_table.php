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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        // user_id devient nullable : un utilisateur peut être connecté, ou non.
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('chambre_id')->constrained()->onDelete('cascade');
        
        // NOUVEAUX CHAMPS pour les clients non connectés
        $table->string('client_nom');
        $table->string('client_prenom');
        $table->string('client_email');
        $table->string('client_telephone');

        $table->date('check_in_date');
        $table->date('check_out_date');
        $table->integer('nombre_invites');
        $table->decimal('prix_total', 10, 2);
        $table->string('statut')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
