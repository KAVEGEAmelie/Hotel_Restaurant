<?php

namespace App\Http\Controllers\Admin;
use App\Models\Chambre;
use App\Models\Reservation;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'chambres' => Chambre::count(),
            'reservations' => Reservation::count(),
            'utilisateurs' => User::count(),
        ];

        // On récupère les 5 dernières réservations avec les infos du client et de la chambre
        $dernieresReservations = Reservation::with(['user', 'chambre', 'adminConfirme'])
                                            ->latest()
                                            ->take(5)
                                            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'dernieresReservations' => $dernieresReservations,
        ]);
    }
}
