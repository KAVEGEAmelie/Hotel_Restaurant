<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChambreImage extends Model
{
    protected $fillable = [
        'chambre_id',
        'image',
        'ordre',
    ];

    public function chambre(): BelongsTo
    {
        return $this->belongsTo(Chambre::class);
    }
}
