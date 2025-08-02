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

    public function store(Request $request) {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // On enlève 'public'. store() utilisera le disque par défaut.
            $validated['image'] = $request->file('image')->store('plats-galerie');
        }
        
        PlatGalerie::create($validated);
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat ajouté.');
    }

    public function edit(PlatGalerie $plats_galery)
    {
        return view('admin.plats-galerie.form', ['plat' => $plats_galery]);
    }

    public function update(Request $request, PlatGalerie $plats_galery)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($plats_galery->image) {
                Storage::disk('public')->delete($plats_galery->image);
            }
            $validated['image'] = $request->file('image')->store('plats-galerie', 'public');
        }

        $plats_galery->update($validated);
        
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat mis à jour avec succès.');
    }

    public function destroy(PlatGalerie $plats_galery)
    {
        if ($plats_galery->image) {
            Storage::disk('public')->delete($plats_galery->image);
        }
        
        $plats_galery->delete();
        
        return redirect()->route('admin.plats-galerie.index')->with('success', 'Plat supprimé.');
    }
}