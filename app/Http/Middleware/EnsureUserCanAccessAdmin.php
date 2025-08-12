<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserCanAccessAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est connecté ET peut accéder à l'admin (admin ou gérant)
        if (Auth::check() && Auth::user()->canAccessAdmin()) {
            return $next($request);
        }

        // Sinon, redirection vers la page d'accueil avec message d'erreur
        return redirect('/')->with('error', "Vous n'avez pas les droits d'accès à cette page.");
    }
}
