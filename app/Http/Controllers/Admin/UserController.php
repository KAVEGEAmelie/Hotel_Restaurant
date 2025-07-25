<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? 1 : 0,
        ]);

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur) // Le nom du paramètre doit correspondre à la route
    {
        return view('admin.users.form', ['user' => $utilisateur]);
    }

    public function update(Request $request, User $utilisateur)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $utilisateur->id],
        ]);

        $utilisateur->name = $request->name;
        $utilisateur->email = $request->email;
        $utilisateur->is_admin = $request->has('is_admin') ? 1 : 0;

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $utilisateur->password = Hash::make($request->password);
        }

        $utilisateur->save();

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $utilisateur)
    {
        // Empêche un admin de se supprimer lui-même
    if (Auth::id() == $utilisateur->id) {
            return redirect()->route('admin.utilisateurs.index')->with('error', 'Vous не pouvez pas supprimer votre propre compte.');
        }

        $utilisateur->delete();
        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
