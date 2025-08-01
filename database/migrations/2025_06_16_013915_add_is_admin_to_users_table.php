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
        Schema::table('users', function (Blueprint $table) {
            // On ajoute la colonne après la colonne 'email'
            $table->boolean('is_admin')->after('email')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // La méthode down pour pouvoir annuler
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
