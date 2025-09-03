<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    /**
     * Servir un fichier depuis le stockage avec gestion d'erreur
     */
    public function serveFile($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            // Retourner une image par défaut ou une erreur 404
            return Response::make('Fichier non trouvé', 404);
        }

        $file = Storage::disk('public')->get($path);
        $fullPath = Storage::disk('public')->path($path);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Vérifier l'existence d'un fichier
     */
    public function checkFile($path)
    {
        $exists = Storage::disk('public')->exists($path);
        
        return response()->json([
            'exists' => $exists,
            'path' => $path,
            'url' => $exists ? asset('storage/' . $path) : null
        ]);
    }
}
