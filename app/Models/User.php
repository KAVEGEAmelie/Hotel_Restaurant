<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email', 
        'telephone',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_admin === true;
    }

    /**
     * Vérifie si l'utilisateur est gérant
     */
    public function isGerant(): bool
    {
        return $this->role === 'gerant';
    }

    /**
     * Vérifie si l'utilisateur peut accéder à l'administration (admin ou gérant)
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isGerant();
    }

    /**
     * Vérifie si l'utilisateur peut gérer les utilisateurs (admin seulement)
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }
}
