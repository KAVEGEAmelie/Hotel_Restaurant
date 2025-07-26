<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public function chambre()
{
    return $this->belongsTo(Chambre::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

protected $fillable = [
    'chambre_id',
    'client_nom',
    'client_prenom',
    'client_email',
    'client_telephone',
    'nombre_invites',
    'check_in_date',
    'check_out_date',
    'user_id',
    'prix_total',
    'statut',
];

}
