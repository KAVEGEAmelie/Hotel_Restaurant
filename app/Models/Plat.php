<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    //
    public function categorie() {
    return $this->belongsTo(Categorie::class);
}
}
