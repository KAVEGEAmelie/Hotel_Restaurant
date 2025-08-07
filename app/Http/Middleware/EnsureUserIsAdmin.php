<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté OU s'il n'est pas admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            // On peut déconnecter l'utilisateur pour plus de sécurité
            Auth::logout();

            // On le redirige vers la page d'accueil avec un message d'erreur
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Si tout est bon, on continue la requête
        return $next($request);
    }
}
