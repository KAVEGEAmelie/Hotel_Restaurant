<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Contrôleurs pour le site public ---
use App\Http\Controllers\PageController;
use App\Http\Controllers\ChambreController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;

// --- Contrôleurs pour le profil utilisateur ---
use App\Http\Controllers\ProfileController;

// --- Contrôleurs pour l'administration ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ChambreController as AdminChambreController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\PlatController as AdminPlatController;
use App\Http\Controllers\Admin\CategorieController as AdminCategorieController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| 1. ROUTES PUBLIQUES (accessibles par tous)
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/chambres', [ChambreController::class, 'index'])->name('chambres.index');
Route::get('/chambres/{chambre}', [ChambreController::class, 'show'])->name('chambres.show');
Route::get('/restaurant', [RestaurantController::class, 'index'])->name('restaurant.index');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'handleContactForm'])->name('contact.submit');


/*
|--------------------------------------------------------------------------
| 2. ROUTES D'AUTHENTIFICATION (Login, Register, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

// === NOUVELLE SECTION POUR LES ROUTES DE RÉSERVATION PUBLIQUES ===
Route::post('/reservation/creer', [ReservationController::class, 'create'])->name('reservation.create');
Route::get('/paiement/{reservation}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/paiement/{reservation}', [PaymentController::class, 'process'])->name('payment.process');

/*
|--------------------------------------------------------------------------
| 3. ROUTES POUR UTILISATEURS CONNECTÉS (Espace Client, Profil)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard de base pour un utilisateur normal
 Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    // Pour tous les autres utilisateurs, on les renvoie à l'accueil.
    return redirect()->route('home');
})->middleware('verified')->name('dashboard');

    // Gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| 4. ROUTES D'ADMINISTRATION (protégées)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('chambres', AdminChambreController::class);
// On définit manuellement la route pour la liste, en la faisant pointer vers notre nouvelle méthode
Route::get('/reservations', [AdminReservationController::class, 'listAll'])->name('reservations.index');
    Route::resource('utilisateurs', AdminUserController::class);

// On peut garder 'resource' pour les autres actions (comme destroy), mais en excluant 'index'
Route::resource('reservations', AdminReservationController::class)->except(['index']);    Route::resource('plats', AdminPlatController::class);
    Route::resource('categories', AdminCategorieController::class);

});
