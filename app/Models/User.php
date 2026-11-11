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
    ];

    // Rôles possibles (colonne users.role). Jamais assignable en masse : à définir explicitement.
    public const ROLE_ADMIN = 'admin';
    public const ROLE_GERANT = 'gerant';
    public const ROLE_CLIENT = 'client';

    public const ROLES = [
        self::ROLE_CLIENT => 'Client',
        self::ROLE_GERANT => 'Gérant',
        self::ROLE_ADMIN => 'Administrateur',
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
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est gérant (accès partiel)
     */
    public function isGerant(): bool
    {
        return $this->role === self::ROLE_GERANT;
    }

    /**
     * Change le rôle (garde la colonne historique is_admin synchronisée).
     */
    public function assignRole(string $role): void
    {
        $this->role = $role;
        $this->is_admin = $role === self::ROLE_ADMIN;
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
