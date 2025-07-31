<?php

namespace App\Http\Middleware;

use App\Services\CashPayService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCashPayConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cashPayService = app(CashPayService::class);
        
        if (!$cashPayService->isConfigured()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Service de paiement non configuré'
                ], 503);
            }
            
            return redirect()->route('home')->with('error', 'Service de paiement temporairement indisponible.');
        }

        return $next($request);
    }
} 