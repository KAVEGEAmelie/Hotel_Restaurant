<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Categorie;
use Illuminate\Http\Request;

class PlatController extends Controller
{
    public function index()
    {
        $plats = Plat::with('categorie')->latest()->paginate(15);
        return view('admin.plats.index', compact('plats'));
    }

    public function create()
    {
        $categories = Categorie::orderBy('nom')->get();
        return view('admin.plats.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_simple' => 'nullable|numeric',
            'prix_menu' => 'nullable|numeric',
            'categorie_id' => 'required|exists:categories,id',
            // 'image' => 'nullable|image' // Si vous ajoutez des images de plats
        ]);

        Plat::create($validated);
        return redirect()->route('admin.plats.index')->with('success', 'Plat créé avec succès.');
    }

    public function edit(Plat $plat)
    {
        $categories = Categorie::orderBy('nom')->get();
        return view('admin.plats.form', compact('plat', 'categories'));
    }

    public function update(Request $request, Plat $plat)
    {
        // ... (Logique de validation et de mise à jour similaire à store) ...
        $plat->update($request->all());
        return redirect()->route('admin.plats.index')->with('success', 'Plat mis à jour avec succès.');
    }

    public function destroy(Plat $plat)
    {
        $plat->delete();
        return redirect()->route('admin.plats.index')->with('success', 'Plat supprimé avec succès.');
    }
}