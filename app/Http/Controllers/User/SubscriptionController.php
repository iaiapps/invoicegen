<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display subscription page
     */
    public function index()
    {
        $user = Auth::user();

        // Get prices from platform settings
        $freeInvoiceLimit = getSetting('free_invoice_limit', 20);
        $basicPrice = getSetting('basic_price', 25000);
        $basicInvoiceLimit = getSetting('basic_invoice_limit', 60);
        $proPrice = getSetting('pro_price', 49000);
        $proInvoiceLimit = getSetting('pro_invoice_limit', 120);

        // Current subscription info
        $currentPlan = [
            'name' => ucfirst($user->subscription_plan),
            'invoice_limit' => $user->invoice_limit,
            'expires_at' => $user->subscription_ends_at,
            'is_free' => $user->subscription_plan === 'free',
            'is_active' => $user->subscription_plan === 'free'
                ? true
                : ($user->subscription_ends_at && $user->subscription_ends_at->isFuture()),
        ];

        // Available plans
        $plans = [
            'free' => [
                'name' => 'Free',
                'price' => 0,
                'price_display' => 'Gratis',
                'invoice_limit' => $freeInvoiceLimit,
                'features' => [
                    $freeInvoiceLimit . ' invoice per bulan',
                    'Unlimited customer',
                    getProductLimit('free') . ' produk',
                    'Custom logo',
                    'WhatsApp integration',
                    'Download PDF',
                ],
                'is_current' => $user->subscription_plan === 'free',
            ],
            'basic' => [
                'name' => 'Basic',
                'price' => $basicPrice,
                'price_display' => 'Rp ' . number_format($basicPrice, 0, ',', '.'),
                'duration' => 'per bulan',
                'invoice_limit' => $basicInvoiceLimit,
                'features' => [
                    $basicInvoiceLimit . ' invoice per bulan',
                    'Unlimited customer',
                    getProductLimit('basic') . ' produk',
                    'Custom logo',
                    'WhatsApp integration',
                    'Download PDF',
                    'Email support',
                ],
                'is_current' => $user->subscription_plan === 'basic',
            ],
            'pro' => [
                'name' => 'Pro',
                'price' => $proPrice,
                'price_display' => 'Rp ' . number_format($proPrice, 0, ',', '.'),
                'duration' => 'per bulan',
                'invoice_limit' => $proInvoiceLimit,
                'features' => [
                    $proInvoiceLimit . ' invoice per bulan',
                    'Unlimited customer',
                    getProductLimit('pro') . ' produk',
                    'Custom logo',
                    'WhatsApp integration',
                    'Download PDF',
                    'Priority email support',
                    'Laporan lengkap',
                ],
                'is_current' => $user->subscription_plan === 'pro',
                'popular' => true,
            ],
        ];

        // Subscription history
        $subscriptionHistory = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate days remaining (only for paid plans)
        // $daysRemaining = null;
        // if (!$currentPlan['is_free'] && $currentPlan['expires_at']) {
        //     $daysRemaining = now()->diffInDays($currentPlan['expires_at'], false);
        // }

        // Bank account info for manual payment (from platform settings)
        $bankAccounts = [];

        // Try to get from JSON format first (new format)
        $bankAccountsJson = getSetting('bank_accounts');
        if ($bankAccountsJson) {
            $bankAccounts = is_string($bankAccountsJson) ? json_decode($bankAccountsJson, true) : $bankAccountsJson;
        }

        // Fallback: Try individual bank settings (legacy format)
        if (empty($bankAccounts)) {
            // Bank 1
            if (getSetting('bank_1_name')) {
                $bankAccounts[] = [
                    'bank' => getSetting('bank_1_name'),
                    'account_number' => getSetting('bank_1_account'),
                    'account_name' => getSetting('bank_1_holder'),
                ];
            }

            // Bank 2
            if (getSetting('bank_2_name')) {
                $bankAccounts[] = [
                    'bank' => getSetting('bank_2_name'),
                    'account_number' => getSetting('bank_2_account'),
                    'account_name' => getSetting('bank_2_holder'),
                ];
            }
        }

        // Final fallback if no bank configured at all
        if (empty($bankAccounts)) {
            $bankAccounts = [
                [
                    'bank' => 'BCA (Belum dikonfigurasi)',
                    'account_number' => '1234567890',
                    'account_name' => 'PT Invoice Generator',
                ],
            ];
        }

        return view('user.subscription.index', compact(
            'user',
            'currentPlan',
            'plans',
            'subscriptionHistory',
            // 'daysRemaining',
            'bankAccounts'
        ));
    }

    /**
     * Upgrade subscription (manual payment - pending approval)
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $plan = $request->plan;

        // Check if trying to downgrade
        if ($user->subscription_plan === 'pro' && $plan === 'basic') {
            return redirect()->back()
                ->with('error', 'Tidak bisa downgrade dari Pro ke Basic. Silakan hubungi support.');
        }

        // Check if already on same plan
        if ($user->subscription_plan === $plan) {
            return redirect()->back()
                ->with('error', 'Anda sudah menggunakan paket ' . ucfirst($plan));
        }

        // Plan details from platform settings
        $planDetails = [
            'basic' => [
                'price' => getSetting('basic_price', 25000),
                'invoice_limit' => getSetting('basic_invoice_limit', 60),
                'product_limit' => getProductLimit('basic'),
                'name' => 'Basic',
            ],
            'pro' => [
                'price' => getSetting('pro_price', 49000),
                'invoice_limit' => getSetting('pro_invoice_limit', 120),
                'product_limit' => getProductLimit('pro'),
                'name' => 'Pro',
            ],
        ];

        $selectedPlan = $planDetails[$plan];

        DB::beginTransaction();
        try {
            // Create subscription record with pending status
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $selectedPlan['price'],
                'payment_method' => 'transfer', // Manual transfer
                'payment_status' => 'pending', // Waiting for admin approval
                'payment_reference' => 'TRX-' . time() . '-' . $user->id,
                'payment_response' => $request->payment_notes,
                'starts_at' => null, // Will be set by admin
                'ends_at' => null, // Will be set by admin
                'invoice_limit' => $selectedPlan['invoice_limit'],
            ]);

            DB::commit();

            return redirect()->route('subscription.index')
                ->with('success', "Permintaan upgrade ke paket {$selectedPlan['name']} berhasil dikirim! Silakan transfer sebesar Rp " . number_format($selectedPlan['price'], 0, ',', '.') . " dan tunggu verifikasi dalam 1x24 jam.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Payment callback (for payment gateway)
     */
    public function paymentCallback(Request $request)
    {
        // This is where Midtrans/payment gateway will send callback
        // For now, just a placeholder

        // Example Midtrans callback handling:
        /*
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if ($hashed == $request->signature_key) {
            $subscription = Subscription::where('transaction_id', $request->order_id)->first();

            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $subscription->update(['payment_status' => 'paid']);
                // Update user subscription status
            } else if ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
                $subscription->update(['payment_status' => 'failed']);
            }
        }
        */

        return response()->json(['status' => 'success']);
    }

    /**
     * Cancel subscription (downgrade to trial at end of period)
     */
    public function cancel()
    {
        $user = Auth::user();

        if ($user->subscription_plan === 'free') {
            return redirect()->back()
                ->with('error', 'Anda sedang menggunakan paket Free, tidak ada subscription yang bisa dibatalkan.');
        }

        // Set to not renew (in real app, this would cancel auto-renewal)
        // For now, we just inform user

        return redirect()->back()
            ->with('warning', 'Untuk membatalkan subscription, silakan hubungi support atau biarkan subscription berakhir pada ' . $user->subscription_ends_at->format('d M Y'));
    }
}
