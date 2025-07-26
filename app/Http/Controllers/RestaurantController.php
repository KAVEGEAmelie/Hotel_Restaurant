<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\MenuPdf; // <-- Ajoutez l'importation
use App\Models\PlatGalerie;


class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Récupère les catégories avec leurs plats
    $categories = Categorie::with('plats')->orderBy('ordre')->get();

    // Récupère le DERNIER menu PDF qui est marqué comme "actif"
$menuActif = MenuPdf::where('est_actif', true)->latest()->first();
    $platsGalerie = PlatGalerie::latest()->take(6)->get();

    return view('pages.restaurant', [
        'categories' => $categories,
        'menuActif' => $menuActif,
        'platsGalerie' => $platsGalerie,
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
