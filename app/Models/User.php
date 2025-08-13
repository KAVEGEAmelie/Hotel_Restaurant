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
        'email', 
        'phone',
        'address',
        'password',
        'is_admin',
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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Vérifie si l'utilisateur est administrateur (accès COMPLET)
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Vérifie si l'utilisateur est gérant (accès partiel)
     */
    public function isGerant(): bool
    {
        return str_contains($this->email, 'gerant') && !$this->is_admin;
    }

    /**
     * Vérifie si l'utilisateur peut accéder au dashboard admin (admin OU gérant)
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isGerant();
    }

    /**
     * Vérifie si l'utilisateur peut gérer les utilisateurs (SEULEMENT admin)
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut donner des permissions (SEULEMENT admin)
     */
    public function canManagePermissions(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut gérer les réservations (admin ET gérant)
     */
    public function canManageReservations(): bool
    {
        return $this->canAccessAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut gérer les chambres (admin ET gérant)
     */
    public function canManageChambres(): bool
    {
        return $this->canAccessAdmin();
    }
}
