<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On vérifie si l'utilisateur est connecté ET si sa colonne is_admin est à true
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request); // Si oui, on le laisse passer
        }

        // Sinon, on le redirige vers la page d'accueil avec une erreur.
        return redirect('/')->with('error', "Vous n'avez pas les droits d'accès à cette page.");
    }
}
