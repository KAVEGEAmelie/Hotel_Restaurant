<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChambreController extends Controller
{
    public function index()
    {
        $chambres = Chambre::latest()->paginate(10);
        return view('admin.chambres.index', compact('chambres'));
    }

    public function create()
    {
        return view('admin.chambres.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:chambres,nom',
            'description_courte' => 'required|string|max:255',
            'description_longue' => 'nullable|string',
            'prix_par_nuit' => 'required|numeric|min:0',
            'capacite' => 'required|integer|min:1',
            'image_principale' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        
        if ($request->hasFile('image_principale')) {
            // Utilise le disque par défaut ('uploads') et stocke dans un sous-dossier 'chambres'.
            // La méthode store() génère un nom unique ET renvoie le chemin complet (ex: 'chambres/nom_unique.jpg').
        $validated['image_principale'] = $request->file('image_principale')->store('chambres', 'public');
        }

        Chambre::create($validated);

        return redirect()->route('admin.chambres.index')->with('success', 'Chambre créée avec succès.');
    }

    public function edit(Chambre $chambre)
    {
        return view('admin.chambres.form', compact('chambre'));
    }

    public function update(Request $request, Chambre $chambre)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:chambres,nom,' . $chambre->id,
            'description_courte' => 'required|string|max:255',
            'description_longue' => 'nullable|string',
            'prix_par_nuit' => 'required|numeric|min:0',
            'capacite' => 'required|integer|min:1',
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);

        if ($request->hasFile('image_principale')) {
            // Supprimer l'ancienne image en utilisant le système de stockage Laravel
            if ($chambre->image_principale) {
                Storage::delete($chambre->image_principale);
            }
            // Uploader la nouvelle image
        $validated['image_principale'] = $request->file('image_principale')->store('chambres', 'public');
        }

        $chambre->update($validated);

        return redirect()->route('admin.chambres.index')->with('success', 'Chambre mise à jour avec succès.');
    }

    public function destroy(Chambre $chambre)
    {
        if ($chambre->image_principale) {
            // Supprimer l'image en utilisant le système de stockage Laravel
         Storage::disk('public')->delete($chambre->image_principale);
        }
        
        $chambre->delete();

        return redirect()->route('admin.chambres.index')->with('success', 'Chambre supprimée avec succès.');
    }
}