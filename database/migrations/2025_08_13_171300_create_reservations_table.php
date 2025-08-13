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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chambre_id')->constrained()->onDelete('cascade');
            
            // Informations client
            $table->string('client_nom');
            $table->string('client_prenom');
            $table->string('client_email');
            $table->string('client_telephone');
            
            // Dates de séjour
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('nombre_invites')->default(1);
            
            // Prix et paiement
            $table->decimal('prix_total', 10, 2);
            $table->enum('statut', ['en_attente', 'confirmee', 'annulee', 'terminee'])->default('en_attente');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echec', 'rembourse'])->default('en_attente');
            $table->decimal('montant_paye', 10, 2)->nullable();
            $table->string('methode_paiement')->nullable();
            $table->datetime('date_paiement')->nullable();
            $table->text('notes_paiement')->nullable();
            
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
