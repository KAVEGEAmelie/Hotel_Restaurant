<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function index() {
    $chambres = \App\Models\Chambre::all();
    return view('chambres.index', ['chambres' => $chambres]);
}
}
