<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInvoiceLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User */

        $user = Auth::user();

        // Skip check untuk admin
        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        // Check invoice limit for trial and basic plans
        if ($user->invoice_count_this_month >= $user->invoice_limit) {
            return redirect()->route('dashboard')
                ->with('error', 'Limit invoice bulan ini sudah tercapai. Upgrade paket Anda untuk membuat lebih banyak invoice.');
        }

        return $next($request);
    }
}
