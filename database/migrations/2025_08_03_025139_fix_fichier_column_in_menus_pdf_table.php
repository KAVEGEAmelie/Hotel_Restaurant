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
    Schema::table('menus_pdf', function (Blueprint $table) {
        // Si la colonne existe mais est mal configurée
        $table->string('fichier')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus_pdf', function (Blueprint $table) {
            //
        });
    }
};
