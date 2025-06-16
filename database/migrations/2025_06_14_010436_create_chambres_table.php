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
    Schema::create('chambres', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // Ex: "Suite Royale"
        $table->string('slug')->unique();
        $table->text('description_courte');
        $table->text('description_longue');
        $table->string('image_principale'); // Chemin vers l'image
        $table->decimal('prix_par_nuit', 8, 2);
        $table->integer('capacite'); // Nombre de personnes max
        $table->boolean('est_disponible')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chambres');
    }
};
