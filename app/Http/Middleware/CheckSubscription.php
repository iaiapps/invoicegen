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
     * Grace Period: Configurable days from platform settings
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

                $gracePeriodDays = (int) \App\Models\PlatformSetting::get('grace_period_days', 7);
                $gracePeriodEnd = $user->subscription_ends_at->copy()->addDays($gracePeriodDays);

                // Masih dalam grace period
                if (now()->lessThanOrEqualTo($gracePeriodEnd)) {
                    $daysLeft = now()->diffInDays($gracePeriodEnd, false);

                    // Allow access, tapi set flash message
                    session()->flash('warning', "Subscription Anda telah berakhir. Anda masih memiliki {$daysLeft} hari masa tenggang sebelum downgrade ke paket Free.");

                    return $next($request);
                }

                // Grace period habis - auto downgrade ke free
                $freeInvoiceLimit = (int) \App\Models\PlatformSetting::get('free_invoice_limit', 30);

                $user->update([
                    'subscription_plan' => 'free',
                    'invoice_limit' => $freeInvoiceLimit,
                    'subscription_ends_at' => null,
                ]);

                return redirect()->route('subscription.index')
                    ->with('info', "Subscription Anda telah berakhir dan downgrade ke paket Free ({$freeInvoiceLimit} invoice/bulan). Silakan upgrade untuk fitur lebih banyak.");
            }
        }

        return $next($request);
    }
}
