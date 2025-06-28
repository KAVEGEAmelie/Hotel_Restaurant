<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
    'user_id', 'chambre_id',
    'client_nom', 'client_prenom', 'client_email', 'client_telephone',
    'check_in_date', 'check_out_date', 'nombre_invites', 'prix_total', 'statut'
];
}
