<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->role);
        $user->save();

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', '👤 Utilisateur créé avec succès !')
            ->with('info', 'Le nouvel utilisateur peut maintenant se connecter');
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->assignRole($request->role);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', '✏️ Utilisateur mis à jour avec succès !')
            ->with('info', 'Les modifications du profil ont été enregistrées');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return redirect()->route('admin.utilisateurs.index')
            ->with('warning', '🗑️ Utilisateur supprimé')
            ->with('info', 'Le compte utilisateur a été définitivement supprimé');
    }
}
