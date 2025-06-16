<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categorie;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On vide les tables pour éviter les doublons si on relance le seeder
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('plats')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Création des catégories
        $sandwichs = Categorie::create(['nom' => 'Nos Sandwichs', 'ordre' => 1]);
        $burgers = Categorie::create(['nom' => 'Nos Burgers', 'ordre' => 2]);
        $assiettes = Categorie::create(['nom' => 'Nos Assiettes', 'ordre' => 3]);
        $chawarma = Categorie::create(['nom' => 'Chawarma', 'ordre' => 4]);

        // === Plats pour la catégorie "Nos Sandwichs" ===
        $sandwichs->plats()->createMany([
            [
                'nom' => 'Kebab',
                'description' => 'Viande ou poulet, salade, tomate, oignon, sauce au choix',
                'prix_simple' => 2000,
                'prix_menu' => 4000
            ],
            [
                'nom' => 'Kebab Galette',
                'description' => 'Pain galette, viande ou poulet, salade, tomate, oignon, sauce au choix',
                'prix_simple' => 2500,
                'prix_menu' => 4000
            ],
            [
                'nom' => 'Américain',
                'description' => 'Steak haché, salade, tomate, fromage, cheddar, sauce cocktail',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Fajitas',
                'description' => 'Poulet, fromage mozzarella, poivron, oignon, champignon, sauce avocat',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Philadelphia',
                'description' => 'Steak, fromage mozzarella, poivron, oignon, champignon',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Twister',
                'description' => 'Pain galette, poulet crispy, salade, tomate, mayonnaise, fromage, cheddar, sauce cocktail',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Hot-Dog New-Yorkais',
                'description' => 'Saucisse, oignon caramélisé, fromage cheddar, chips, ketchup, mayonnaise, moutarde',
                'prix_simple' => 2000,
                'prix_menu' => 4000
            ],
            [
                'nom' => 'Bollywood',
                'description' => 'Poulet mariné sauce curry, salade, maïs, fromage mozzarella, mayonnaise, sauce cocktail',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Oriental',
                'description' => 'Merguez, sauce tomate, oignon, sauce piquant',
                'prix_simple' => 2000,
                'prix_menu' => 4000
            ],
        ]);

        // === Plats pour la catégorie "Nos Burgers" ===
        $burgers->plats()->createMany([
            [
                'nom' => 'Classic Burgers',
                'description' => 'Viande, salade, tomate, oignon, fromage cheddar, sauce cocktail',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Crispy Burgers',
                'description' => 'Viande, salade, tomate, oignon, fromage cheddar, sauce cocktail',
                'prix_simple' => 3000,
                'prix_menu' => 5000
            ],
            [
                'nom' => 'Barbecue Burgers',
                'description' => 'Steak haché, salade, oignon caramélisé, fromage cheddar, mayonnaise',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
            [
                'nom' => 'Royal Burgers',
                'description' => 'Steak haché, fromage cheddar, cornichon, oignon, salade coleslaw, sauce cocktail',
                'prix_simple' => 2500,
                'prix_menu' => 4500
            ],
        ]);

        // === Plats pour la catégorie "Nos Assiettes" ===
        $assiettes->plats()->createMany([
            [
                'nom' => 'Assiettes Kebab',
                'description' => 'Viande kebab, salade, tomate, oignon, frite, pain. Boisson comprise.',
                'prix_menu' => 6000
            ],
            [
                'nom' => 'Assiettes Poulet Crispy',
                'description' => '5 pièces de poulet crispy, salade coleslaw, frite, sauce au choix. Boisson comprise.',
                'prix_menu' => 6000
            ],
            [
                'nom' => 'Salade Kebab',
                'description' => 'Viande kebab, oignon, tomate, maïs, chou rouge, concombre, sauce blanche. Boisson comprise.',
                'prix_menu' => 5000
            ],
        ]);
        
        // === Plats pour la catégorie "Chawarma" ===
        $chawarma->plats()->createMany([
            [
                'nom' => 'Chawarma Viande',
                'description' => null,
                'prix_simple' => 2000,
            ],
            [
                'nom' => 'Chawarma Poulet',
                'description' => null,
                'prix_simple' => 2500,
            ],
        ]);
    }
}