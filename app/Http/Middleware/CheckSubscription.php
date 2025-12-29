<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * 
     * Grace Period: 7 days after expiry before downgrade to free
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Skip check untuk admin
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check subscription expired with grace period
        if (in_array($user->subscription_plan, ['basic', 'pro'])) {
            if ($user->subscription_ends_at && now()->greaterThan($user->subscription_ends_at)) {
                
                $gracePeriodEnd = $user->subscription_ends_at->copy()->addDays(7);
                
                // Masih dalam grace period (7 hari)
                if (now()->lessThanOrEqualTo($gracePeriodEnd)) {
                    $daysLeft = now()->diffInDays($gracePeriodEnd, false);
                    
                    // Allow access, tapi set flash message
                    session()->flash('warning', "Subscription Anda telah berakhir. Anda masih memiliki {$daysLeft} hari masa tenggang sebelum downgrade ke paket Free.");
                    
                    return $next($request);
                }
                
                // Grace period habis - auto downgrade ke free
                $user->update([
                    'subscription_plan' => 'free',
                    'invoice_limit' => 30,
                    'subscription_ends_at' => null,
                ]);
                
                return redirect()->route('subscription')
                    ->with('info', 'Subscription Anda telah berakhir dan downgrade ke paket Free (30 invoice/bulan). Silakan upgrade untuk fitur lebih banyak.');
            }
        }

        return $next($request);
    }
}
