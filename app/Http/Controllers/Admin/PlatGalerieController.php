<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatGalerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatGalerieController extends Controller
{
    public function index()
    {
        $plats = PlatGalerie::latest()->paginate(10);
        return view('admin.plats-galerie.index', compact('plats'));
    }

    public function create()
    {
        return view('admin.plats-galerie.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('plats-galerie', 'uploads');
        }
        PlatGalerie::create($validated);
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat ajouté à la galerie.');
    }

    /**
     * Affiche le formulaire pour modifier un plat existant.
     * C'EST CETTE MÉTHODE QUI MANQUAIT.
     */
    public function edit(PlatGalerie $plat)
    {
        return view('admin.plats-galerie.form', compact('plat'));
    }

    /**
     * Met à jour un plat existant.
     * C'EST CETTE MÉTHODE QUI MANQUAIT AUSSI.
     */
   public function update(Request $request, PlatGalerie $plat)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($plat->image) {
                Storage::disk('uploads')->delete($plat->image);
            }
            $validated['image'] = $request->file('image')->store('plats-galerie', 'uploads');
        }

        $plat->update($validated);
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat mis à jour avec succès.');
    }

    // CORRECTION ICI
    public function destroy(PlatGalerie $plat)
    {
        Storage::disk('uploads')->delete($plat->image);
        $plat->delete();
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat supprimé.');
    }
}
