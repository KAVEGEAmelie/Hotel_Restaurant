<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('plats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('categorie_id')->constrained()->onDelete('cascade');
        $table->string('nom'); // Ex: "Classic Burgers"
        $table->text('description')->nullable();
        $table->decimal('prix_simple', 8, 2)->nullable();
        $table->decimal('prix_menu', 8, 2)->nullable();
        $table->string('image')->nullable(); // Chemin vers l'image du plat
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plats');
    }
};
