<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::dropIfExists('menus_pdf');

    Schema::create('menus_pdf', function (Blueprint $table) {
        $table->id();
        $table->string('titre');
        $table->string('fichier'); // La colonne manquante
        $table->boolean('est_actif')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
