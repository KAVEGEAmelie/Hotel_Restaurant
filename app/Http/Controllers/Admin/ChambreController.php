<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChambreController extends Controller
{
    /**
     * Affiche la liste de toutes les chambres.
     */
    public function index()
    {
        $chambres = Chambre::latest()->paginate(10);
        return view('admin.chambres.index', compact('chambres'));
    }

    /**
     * Affiche le formulaire pour créer une nouvelle chambre.
     */
    public function create()
    {
        return view('admin.chambres.form');
    }

    /**
     * Enregistre une nouvelle chambre dans la base de données.
     */
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
        $image = $request->file('image_principale');
        $nomImage = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        // Déplace dans public/uploads/chambres
        $image->move(public_path('uploads/chambres'), $nomImage);
        $validated['image_principale'] = $nomImage; // stocke juste le nom
    }

        Chambre::create($validated);

        return redirect()->route('admin.chambres.index')->with('success', 'Chambre créée avec succès.');
    }

    /**
     * Affiche le formulaire pour modifier une chambre existante.
     */
    public function edit(Chambre $chambre)
    {
        return view('admin.chambres.form', compact('chambre'));
    }

    /**
     * Met à jour une chambre existante dans la base de données.
     */
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
        // Supprimer l’ancienne image si elle existe
        $ancienImage = public_path('uploads/chambres/' . $chambre->image_principale);
        if (file_exists($ancienImage)) {
            unlink($ancienImage);
        }
        $image = $request->file('image_principale');
        $nomImage = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/chambres'), $nomImage);
        $validated['image_principale'] = $nomImage;
    }

        $chambre->update($validated);

        return redirect()->route('admin.chambres.index')->with('success', 'Chambre mise à jour avec succès.');
    }

    /**
     * Supprime une chambre de la base de données.
     */
    public function destroy(Chambre $chambre)
{
    if ($chambre->image_principale) {
        $ancienImage = public_path('uploads/chambres/' . $chambre->image_principale);
        if (file_exists($ancienImage)) {
            unlink($ancienImage);
        }
    }

    $chambre->delete();

    return redirect()->route('admin.chambres.index')->with('success', 'Chambre supprimée avec succès.');
}

}
