<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatGalerie extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * On force Laravel à utiliser ce nom de table, au lieu de deviner.
     *
     * @var string
     */
    protected $table = 'plat_galeries'; // <-- AJOUTEZ CETTE LIGNE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'image',
    ];
}
