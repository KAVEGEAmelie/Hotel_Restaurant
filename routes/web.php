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

// --- Contrôleurs Admin ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ChambreController as AdminChambreController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MenuPdfController as AdminMenuPdfController;
use App\Http\Controllers\Admin\PlatGalerieController as AdminPlatGalerieController;

/*
|--------------------------------------------------------------------------
| 1. ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');

// Pages principales
Route::get('/chambres', [ChambreController::class, 'listPublic'])->name('chambres.index');
Route::get('/chambres/{chambre}', [ChambreController::class, 'show'])->name('chambres.show');
Route::get('/restaurant', [RestaurantController::class, 'index'])->name('restaurant.index');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'handleContactForm'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| ROUTES DE RÉSERVATION ET PAIEMENT
|--------------------------------------------------------------------------
*/

// Création et stockage de réservation
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

// Affichage confirmation avant paiement
Route::get('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');

// Nouvelles routes pour bon de réservation
Route::get('/reservations/{reservation}/bon', [ReservationController::class, 'bon'])->name('reservations.bon');
Route::get('/reservations/{reservation}/download-bon', [ReservationController::class, 'downloadBon'])->name('reservations.download-bon');

// Routes de paiement CashPay
Route::post('/payment/initiate/{reservation}', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::get('/payment/return/{reservation}', [PaymentController::class, 'paymentReturn'])->name('payment.return');
Route::get('/payment/success/{reservation}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed/{reservation}', [PaymentController::class, 'failed'])->name('payment.failed');
Route::get('/payment/receipt/{reservation}', [PaymentController::class, 'downloadReceipt'])->name('payment.receipt');
Route::get('/payment/status/{reservation}', [PaymentController::class, 'checkPaymentStatus'])->name('payment.status');

// Test CashPay authentication
Route::get('/payment/test-cashpay', [PaymentController::class, 'testCashPay'])->name('payment.test');

// Webhook CashPay (sans middleware auth)
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');

// Page de succès de réservation
Route::get('/reservation/succes/{reservation}', function (Reservation $reservation) {
    return view('reservation.success', compact('reservation'));
})->name('reservation.success');
/*
|--------------------------------------------------------------------------
| 2. ROUTES D'AUTHENTIFICATION
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| 3. ROUTES UTILISATEURS CONNECTÉS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/reservation/{reservation}/recu', [ReservationController::class, 'downloadReceipt'])->name('reservation.receipt');
});

/*
|--------------------------------------------------------------------------
| 4. ROUTES D'ADMINISTRATION
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Routes des chambres (accessible aux admin ET gérants)
    Route::resource('chambres', AdminChambreController::class);
    Route::post('/chambres/bulk-action', [AdminChambreController::class, 'bulkAction'])->name('chambres.bulk-action');
    Route::patch('/chambres/{chambre}/toggle-status', [AdminChambreController::class, 'toggleStatus'])->name('chambres.toggle-status');

    // Routes des réservations (accessible aux admin ET gérants)
    Route::get('/reservations', [AdminReservationController::class, 'listAll'])->name('reservations.index');
    Route::resource('reservations', AdminReservationController::class)->except(['index']);
    Route::get('/reservations/{reservation}/fiche-police', [AdminReservationController::class, 'downloadPoliceForm'])->name('reservations.police_form');
    Route::post('/reservations/bulk-action', [AdminReservationController::class, 'bulkAction'])->name('reservations.bulkAction');
    Route::patch('/reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::get('/reservations/{reservation}/recu', [AdminReservationController::class, 'downloadReceipt'])->name('reservations.receipt');
    Route::get('/reservations/export/{format}', [AdminReservationController::class, 'export'])->name('reservations.export');

    // Routes des menus PDF (accessible aux admin ET gérants)
    Route::resource('menus-pdf', AdminMenuPdfController::class);

    // Routes de la galerie des plats (accessible aux admin ET gérants)
    Route::resource('plats-galerie', AdminPlatGalerieController::class)->parameters(['plats-galerie' => 'plat']);

    // Routes CashPay pour test et gestion
    Route::prefix('cashpay')->name('cashpay.')->group(function () {
        Route::get('/', function () {
            return view('admin.cashpay.test');
        })->name('index');
        Route::get('/test', [PaymentController::class, 'testCashPayAuth'])->name('test');
        Route::get('/config', [PaymentController::class, 'showCashPayConfig'])->name('config');
        Route::get('/gateways', [PaymentController::class, 'getCashPayGateways'])->name('gateways');
        Route::post('/create-ledger', [PaymentController::class, 'createCashPayLedger'])->name('create-ledger');
        Route::get('/ledgers', [PaymentController::class, 'listCashPayLedgers'])->name('ledgers');
        Route::post('/close-ledger/{reference}', [PaymentController::class, 'closeCashPayLedger'])->name('close-ledger');
    });

    // Routes des utilisateurs (SEULEMENT pour les administrateurs)
    Route::middleware(['admin.manage.users'])->group(function () {
        Route::resource('utilisateurs', AdminUserController::class)->parameters(['utilisateurs' => 'user']);
    });
});
