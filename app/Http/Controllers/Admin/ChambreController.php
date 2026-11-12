<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use App\Models\ChambreImage;
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

    public function show(Chambre $chambre)
    {
        return view('admin.chambres.show', compact('chambre'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:chambres,nom',
            'description_courte' => 'required|string|max:500',
            'description_longue' => 'required|string',
            'prix_par_nuit' => 'required|numeric|min:0',
            'capacite' => 'required|integer|min:1',
            'image_principale' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);

        if ($request->hasFile('image_principale')) {
            // La méthode store() génère un nom unique ET renvoie le chemin complet (ex: 'chambres/nom_unique.jpg').
        $validated['image_principale'] = $request->file('image_principale')->store('chambres', 'public');
        }

        $chambre = Chambre::create(collect($validated)->except('images')->toArray());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $chambre->images()->create([
                    'image' => $file->store('chambres/galerie', 'public'),
                    'ordre' => $i,
                ]);
            }
        }

        return redirect()->route('admin.chambres.index')
            ->with('success', '🏨 Chambre créée avec succès !')
            ->with('info', 'La chambre est maintenant disponible pour les réservations');
    }

    public function edit(Chambre $chambre)
    {
        return view('admin.chambres.form', compact('chambre'));
    }

    public function update(Request $request, Chambre $chambre)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:chambres,nom,' . $chambre->id,
            'description_courte' => 'required|string|max:500',
            'description_longue' => 'nullable|string',
            'prix_par_nuit' => 'required|numeric|min:0',
            'capacite' => 'required|integer|min:1',
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);

        if ($request->hasFile('image_principale')) {
            // Supprimer l'ancienne image en utilisant le système de stockage Laravel
            if ($chambre->image_principale) {
                Storage::delete($chambre->image_principale);
            }
        $validated['image_principale'] = $request->file('image_principale')->store('chambres', 'public');
        }

        $chambre->update(collect($validated)->except('images')->toArray());

        if ($request->hasFile('images')) {
            $ordre = (int) $chambre->images()->max('ordre') + 1;
            foreach ($request->file('images') as $file) {
                $chambre->images()->create([
                    'image' => $file->store('chambres/galerie', 'public'),
                    'ordre' => $ordre++,
                ]);
            }
        }

        return redirect()->route('admin.chambres.index')
            ->with('success', '✏️ Chambre mise à jour avec succès !')
            ->with('info', 'Les modifications ont été enregistrées');
    }

    public function destroy(Chambre $chambre)
    {
        if ($chambre->image_principale) {
            // Supprimer l'image en utilisant le système de stockage Laravel
         Storage::disk('public')->delete($chambre->image_principale);
        }

        foreach ($chambre->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $chambre->delete();

        return redirect()->route('admin.chambres.index')
            ->with('warning', '🗑️ Chambre supprimée')
            ->with('info', 'La chambre a été retirée du système');
    }

    /**
     * Toggle le statut de disponibilité d'une chambre
     */
    public function toggleStatus(Chambre $chambre)
    {
        // Déterminer le nouveau statut
        $currentStatus = $chambre->est_disponible ?? true;
        $newStatus = !$currentStatus;

        // Mettre à jour le statut
        $chambre->update(['est_disponible' => $newStatus]);

        $statusText = $newStatus ? 'disponible' : 'indisponible';
        $emoji = $newStatus ? '✅' : '❌';

        return response()->json([
            'success' => true,
            'message' => "{$emoji} Chambre '{$chambre->nom}' marquée comme {$statusText}",
            'new_status' => $newStatus,
            'status_text' => $statusText
        ]);
    }

    /**
     * Supprimer une image de la galerie d'une chambre
     */
    public function destroyImage(ChambreImage $image)
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', '🗑️ Image supprimée de la galerie');
    }
}
