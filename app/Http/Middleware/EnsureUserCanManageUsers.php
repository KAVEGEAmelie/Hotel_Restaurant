<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserCanManageUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est connecté ET peut gérer les utilisateurs (admin seulement)
        if (Auth::check() && Auth::user()->canManageUsers()) {
            return $next($request);
        }

        // Sinon, redirection vers le dashboard admin avec message d'erreur
        return redirect()->route('admin.dashboard')->with('error', "Seuls les administrateurs peuvent gérer les utilisateurs.");
    }
}
