<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with('user');

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('shop_name', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total_revenue' => Subscription::where('payment_status', 'paid')->sum('amount'),
            'revenue_this_month' => Subscription::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'pending_payments' => Subscription::where('payment_status', 'pending')->count(),
            'total_subscriptions' => Subscription::count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Approve subscription (manual payment verification)
     */
    public function approve(Request $request, Subscription $subscription)
    {
        if ($subscription->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Subscription ini tidak dalam status pending.');
        }

        $request->validate([
            'duration_months' => 'required|integer|min:1|max:12',
        ]);

        $durationMonths = (int) $request->duration_months;

        try {
            DB::beginTransaction();

            $startsAt = now();
            $endsAt = now()->addMonths($durationMonths);

            // Update subscription
            $subscription->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            // Update user
            $user = $subscription->user;
            $user->update([
                'subscription_plan' => $subscription->plan,
                'subscription_ends_at' => $endsAt,
                'invoice_limit' => $subscription->invoice_limit,
                'product_limit' => getProductLimit($subscription->plan),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Subscription {$subscription->plan} untuk {$user->name} berhasil diaktifkan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject subscription
     */
    public function reject(Request $request, Subscription $subscription)
    {
        if ($subscription->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Subscription ini tidak dalam status pending.');
        }

        $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        try {
            $subscription->update([
                'payment_status' => 'failed',
                'payment_response' => 'Rejected: ' . ($request->reject_reason ?? 'No reason provided'),
            ]);

            return redirect()->back()
                ->with('success', 'Subscription berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
