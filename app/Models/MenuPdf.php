<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPdf extends Model
{
    use HasFactory;

    protected $table = 'menus_pdf'; // Précise le nom de la table

    protected $fillable = [
        'titre',
        'fichier_pdf',
        'est_actif',
    ];
}