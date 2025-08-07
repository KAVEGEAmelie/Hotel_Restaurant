<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::orderBy('ordre')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'ordre' => 'required|integer',
        ]);
        Categorie::create($validated);
        return redirect()->route('admin.categories.index')
            ->with('success', '📂 Catégorie créée avec succès !')
            ->with('info', 'Nouvelle catégorie disponible pour les plats');
    }

    public function edit(Categorie $category) // Laravel 11 utilise 'category' par défaut
    {
        return view('admin.categories.form', ['categorie' => $category]);
    }

    public function update(Request $request, Categorie $category)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom,' . $category->id,
            'ordre' => 'required|integer',
        ]);
        $category->update($validated);
        return redirect()->route('admin.categories.index')
            ->with('success', '✏️ Catégorie mise à jour avec succès !')
            ->with('info', 'Les modifications ont été enregistrées');
    }

    public function destroy(Categorie $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('warning', '🗑️ Catégorie supprimée')
            ->with('info', 'La catégorie a été retirée du système');
    }
}
