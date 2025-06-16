<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChambreController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RestaurantController;

Route::get('/', function () {
    return view('home');
})->name('home');

//Route pour chambre
Route::get('/chambres', 
[ChambreController::class, 'index'])
->name('chambres.index');

//Route pour restaurant
Route::get('/restaurant', function () {
    return "Page du Restaurant à venir...";
})->name('restaurant.index');

//Route pour a-propos
Route::get('/a-propos', 
[PageController::class, 'about'])
->name('about');

//Route pour contact
Route::get('/contact', 
[PageController::class, 'contact'])
->name('contact');

//Route pour traiter l'envoi du formulaire de contact
Route::post('/contact', 
[PageController::class, 'handleContactForm'])
->name('contact.submit');

//Route pour restaurant
Route::get('/restaurant', 
[RestaurantController::class, 'index'])
->name('restaurant.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
