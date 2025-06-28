<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chambre extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'slug',
        'description_courte',
        'description_longue',
        'image_principale',
        'prix_par_nuit',
        'capacite',
        'est_disponible', // Même si on ne le met pas dans le formulaire, c'est bien de l'avoir ici
    ];

    // ... vos relations (comme la relation vers Reservation) ...
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}