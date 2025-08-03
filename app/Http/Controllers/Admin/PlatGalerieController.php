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
            // ON UTILISE LE DISQUE 'public'
            $validated['image'] = $request->file('image')->store('plats-galerie', 'public');
        }

        PlatGalerie::create($validated);

        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat ajouté à la galerie.');
    }

    public function edit(PlatGalerie $plat)
    {
        return view('admin.plats-galerie.form', compact('plat'));
    }

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
                // ON UTILISE LE DISQUE 'public'
                Storage::disk('public')->delete($plat->image);
            }
            $validated['image'] = $request->file('image')->store('plats-galerie', 'public');
        }

        $plat->update($validated);

        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat mis à jour avec succès.');
    }

    public function destroy(PlatGalerie $plat)
    {
        if ($plat->image) {
            // ON UTILISE LE DISQUE 'public'
            Storage::disk('public')->delete($plat->image);
        }

        $plat->delete();

        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat supprimé.');
    }
}
