<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuPdfController extends Controller
{
    public function index() {
        $menus_pdf = MenuPdf::latest()->get();
        return view('admin.menus-pdf.index', compact('menus_pdf'));
    }

    public function create() {
        return view('admin.menus-pdf.form');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'fichier' => 'required|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('fichier')) {
            $validated['fichier'] = $request->file('fichier')->store('menus', 'uploads');
        }

        if ($request->has('est_actif')) {
            MenuPdf::where('est_actif', true)->update(['est_actif' => false]);
            $validated['est_actif'] = true;
        }

        MenuPdf::create($validated);
        return redirect()->route('admin.menus-pdf.index')->with('success', 'Menu ajouté.');
    }

    public function destroy(MenuPdf $menus_pdf) {
        Storage::disk('uploads')->delete($menus_pdf->fichier);
        $menus_pdf->delete();
        return redirect()->route('admin.menus-pdf.index')->with('success', 'Menu supprimé.');
    }
}
