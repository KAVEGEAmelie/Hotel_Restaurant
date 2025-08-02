<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
// --- Contrôleurs ---
use App\Http\Controllers\PageController;
use App\Http\Controllers\ChambreController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

// --- Contrôleurs Admin (avec alias) ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ChambreController as AdminChambreController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\PlatController as AdminPlatController;
use App\Http\Controllers\Admin\CategorieController as AdminCategorieController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MenuPdfController as AdminMenuPdfController;
use App\Http\Controllers\Admin\PlatGalerieController as AdminPlatGalerieController;
/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/chambres', [ChambreController::class, 'listPublic'])->name('chambres.index');
Route::get('/chambres/{chambre}', [ChambreController::class, 'show'])->name('chambres.show');
Route::get('/restaurant', [RestaurantController::class, 'index'])->name('restaurant.index');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'handleContactForm'])->name('contact.submit');

// Réservation publique
Route::post('/reservation/creer', [ReservationController::class, 'create'])->name('reservation.create');
Route::middleware(['cashpay.config'])->group(function () {
    Route::get('/paiement/{reservation}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/paiement/{reservation}', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/test-connection', [PaymentController::class, 'testConnection'])->name('payment.test');
});

Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/return', [PaymentController::class, 'return'])->name('payment.return');
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/reservation/{reservation}/recu', [ReservationController::class, 'downloadReceipt'])->name('reservation.receipt');
Route::get('/reservation/succes/{reservation}', function (Reservation $reservation) {
    // Sécurité : On s'assure que la session contient une clé qui prouve que le paiement vient d'être fait
    // if (!session('payment_success_for_' . $reservation->id)) { abort(404); }
    return view('reservation.success', compact('reservation'));
})->name('reservation.success');
/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION ET UTILISATEURS CONNECTÉS
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ROUTES D'ADMINISTRATION (protégées)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('chambres', AdminChambreController::class);

    // Pour les réservations, on utilise la méthode manuelle pour contourner le bug
    Route::get('/reservations', [AdminReservationController::class, 'listAll'])->name('reservations.index');
    Route::resource('reservations', AdminReservationController::class)->except(['index']);

    Route::get('/reservations/{reservation}/fiche-police', [AdminReservationController::class, 'downloadPoliceForm'])->name('reservations.police_form');

    Route::resource('plats', AdminPlatController::class);
    Route::resource('categories', AdminCategorieController::class);

    Route::resource('menus-pdf', AdminMenuPdfController::class);
Route::resource('plats-galerie', AdminPlatGalerieController::class)->parameters([
    'plats-galerie' => 'plat' // Dit à Laravel que le paramètre s'appelle 'plat'
]);
    // LA BONNE ROUTE POUR LES UTILISATEURS
    Route::resource('utilisateurs', AdminUserController::class)->parameters([
        'utilisateurs' => 'user' // Force le paramètre à s'appeler 'user'
    ]);

});
