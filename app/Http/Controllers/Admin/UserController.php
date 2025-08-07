<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        return view('admin.users.form');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', '👤 Utilisateur créé avec succès !')
            ->with('info', 'Le nouvel utilisateur peut maintenant se connecter');
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(User $user) // CORRIGÉ
    {
        return view('admin.users.form', compact('user')); // CORRIGÉ
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user) // CORRIGÉ
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id], // CORRIGÉ
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name; // CORRIGÉ
        $user->email = $request->email; // CORRIGÉ
        $user->is_admin = $request->has('is_admin'); // CORRIGÉ

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password); // CORRIGÉ
        }

        $user->save(); // CORRIGÉ

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', '✏️ Utilisateur mis à jour avec succès !')
            ->with('info', 'Les modifications du profil ont été enregistrées');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user) // CORRIGÉ
    {
        if (Auth::id() == $user->id) { // CORRIGÉ
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete(); // CORRIGÉ
        return redirect()->route('admin.utilisateurs.index')
            ->with('warning', '🗑️ Utilisateur supprimé')
            ->with('info', 'Le compte utilisateur a été définitivement supprimé');
    }
}
