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
    Schema::create('menus_pdf', function (Blueprint $table) {
        $table->id();
        $table->string('titre'); // Ex: "Menu Principal 2024", "Carte des Vins"
        $table->string('fichier_pdf'); // Stockera le chemin du fichier (ex: 'menus/menu-2024.pdf')
        $table->boolean('est_actif')->default(false); // Pour n'afficher que le menu actuel
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_pdfs');
    }
};
