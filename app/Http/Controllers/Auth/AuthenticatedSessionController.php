<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authentifier l'utilisateur
        $request->authenticate();

        // Régénérer la session pour sécurité
        Session::regenerate();

        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Redirection selon le type d'utilisateur avec notifications modernes
        if ($user && $user->canAccessAdmin()) {
            $roleTitle = $user->isAdmin() ? 'administrateur' : 'gérant';
            return redirect()->route('admin.dashboard')
                ->with('notification', [
                    'type' => 'success',
                    'title' => '👋 Bienvenue dans l\'administration !',
                    'message' => "Connexion réussie en tant que {$roleTitle} • " . $user->name,
                    'duration' => 6000
                ])
                ->with('notification_secondary', [
                    'type' => 'info',
                    'title' => '🛡️ Accès ' . $roleTitle . ' activé',
                    'message' => 'Vous avez maintenant accès aux fonctionnalités d\'administration',
                    'duration' => 4000
                ]);
        }

        // Redirection utilisateur normal
        return redirect()->intended('/')
            ->with('notification', [
                'type' => 'success',
                'title' => '🎉 Connexion réussie !',
                'message' => "Bienvenue " . ($user ? $user->name : '') . " sur le site de l'Hôtel Le Printemps",
                'duration' => 5000
            ])
            ->with('notification_secondary', [
                'type' => 'reservation',
                'title' => '🏨 Prêt pour votre réservation',
                'message' => 'Découvrez nos chambres et services exceptionnels',
                'duration' => 4000
            ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Récupérer le nom avant la déconnexion
        $userName = Auth::user()->name ?? 'Utilisateur';

        // Déconnexion sécurisée
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        // Redirection avec notifications de déconnexion
        return redirect('/')
            ->with('notification', [
                'type' => 'info',
                'title' => '👋 Déconnexion réussie',
                'message' => "Au revoir {$userName} ! Merci de votre visite",
                'duration' => 4000
            ])
            ->with('notification_secondary', [
                'type' => 'success',
                'title' => '🔒 Session fermée en sécurité',
                'message' => 'À bientôt sur le site de l\'Hôtel Le Printemps !',
                'duration' => 5000
            ]);
    }
}
