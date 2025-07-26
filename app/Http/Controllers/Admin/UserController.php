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
        // On ne veut pas que l'admin actuel apparaisse dans la liste pour éviter qu'il se supprime
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

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(User $utilisateur)
    {
        return view('admin.users.form', ['user' => $utilisateur]);
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $utilisateur)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$utilisateur->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Le mot de passe est optionnel
        ]);

        $utilisateur->name = $request->name;
        $utilisateur->email = $request->email;
        $utilisateur->is_admin = $request->has('is_admin');

        // On ne met à jour le mot de passe que s'il a été rempli
        if ($request->filled('password')) {
            $utilisateur->password = Hash::make($request->password);
        }

        $utilisateur->save();

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $utilisateur)
{
    // Ligne corrigée
    if (Auth::id() == $utilisateur->id) {
        return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    }

    $utilisateur->delete();
    return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur supprimé avec succès.');
}
}
