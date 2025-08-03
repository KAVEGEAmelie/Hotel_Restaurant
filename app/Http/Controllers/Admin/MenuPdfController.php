<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuPdfController extends Controller
{
    public function index()
    {
        $menus_pdf = MenuPdf::latest()->paginate(10);
        return view('admin.menus-pdf.index', compact('menus_pdf'));
    }

    public function create()
    {
        return view('admin.menus-pdf.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'fichier' => 'required|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('fichier')) {
            // On stocke sur le disque 'public' (le disque par défaut de Laravel pour les fichiers publics)
            $validated['fichier'] = $request->file('fichier')->store('menus', 'public');
        }

        if ($request->has('est_actif')) {
            MenuPdf::where('est_actif', true)->update(['est_actif' => false]);
            $validated['est_actif'] = true;
        }

        MenuPdf::create($validated);
        return redirect()->route('admin.menus-pdf.index')->with('success', 'Menu ajouté avec succès.');
    }

    public function edit(MenuPdf $menus_pdf)
    {
        return view('admin.menus-pdf.form', ['menu_pdf' => $menus_pdf]);
    }

    public function update(Request $request, MenuPdf $menus_pdf)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'fichier' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier du disque 'public'
            if ($menus_pdf->fichier) {
                Storage::disk('public')->delete($menus_pdf->fichier);
            }
            // Uploader le nouveau sur le disque 'public'
            $validated['fichier'] = $request->file('fichier')->store('menus', 'public');
        }

        if ($request->has('est_actif')) {
            MenuPdf::where('est_actif', true)->update(['est_actif' => false]);
            $validated['est_actif'] = true;
        } else {
            $validated['est_actif'] = false;
        }

        $menus_pdf->update($validated);
        return redirect()->route('admin.menus-pdf.index')->with('success', 'Menu mis à jour avec succès.');
    }

    public function destroy(MenuPdf $menus_pdf)
    {
        // Supprimer le fichier du disque 'public'
        if ($menus_pdf->fichier) {
            Storage::disk('public')->delete($menus_pdf->fichier);
        }

        $menus_pdf->delete();
        return redirect()->route('admin.menus-pdf.index')->with('success', 'Menu supprimé avec succès.');
    }
}
