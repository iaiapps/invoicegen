<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('id', '!=', auth()->id())
            ->with('roles');

        // Filter by subscription plan
        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('shop_name', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['invoices', 'customers', 'products', 'subscriptions']);

        $stats = [
            'total_invoices' => $user->invoices->count(),
            'paid_invoices' => $user->invoices->where('status', 'paid')->count(),
            'total_revenue' => $user->invoices->where('status', 'paid')->sum('total'),
            'total_customers' => $user->customers->count(),
            'total_products' => $user->products->count(),
        ];

        $recentInvoices = $user->invoices()->with('customer')->latest()->take(10)->get();
        $recentSubscriptions = $user->subscriptions()->latest()->take(5)->get();

        return view('admin.users.show', compact('user', 'stats', 'recentInvoices', 'recentSubscriptions'));
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'aktif' : 'nonaktif';
        
        return redirect()->back()->with('success', "User {$user->name} berhasil di{$status}kan!");
    }

    /**
     * Manual upgrade/downgrade user subscription
     */
    public function updateSubscription(Request $request, User $user)
    {
        $request->validate([
            'plan' => 'required|in:free,basic,pro',
            'duration_months' => 'required_if:plan,basic,pro|nullable|integer|min:1|max:12',
        ]);

        $plan = $request->plan;
        
        // Get limits from platform settings
        $invoiceLimits = [
            'free' => (int) \App\Models\PlatformSetting::get('free_invoice_limit', 30),
            'basic' => (int) \App\Models\PlatformSetting::get('basic_invoice_limit', 60),
            'pro' => (int) \App\Models\PlatformSetting::get('pro_invoice_limit', 120),
        ];
        
        $productLimits = [
            'free' => (int) \App\Models\PlatformSetting::get('free_product_limit', 50),
            'basic' => (int) \App\Models\PlatformSetting::get('basic_product_limit', 200),
            'pro' => (int) \App\Models\PlatformSetting::get('pro_product_limit', 9999),
        ];

        try {
            $data = [
                'subscription_plan' => $plan,
                'invoice_limit' => $invoiceLimits[$plan],
                'product_limit' => $productLimits[$plan],
            ];

            // Set subscription end date for paid plans
            if (in_array($plan, ['basic', 'pro']) && $request->duration_months) {
                $data['subscription_ends_at'] = now()->addMonths((int) $request->duration_months);
            } else {
                $data['subscription_ends_at'] = null; // Free plan
            }

            $user->update($data);

            return redirect()->back()
                ->with('success', "User berhasil diubah ke plan {$plan}!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Extend subscription (add more months)
     */
    public function extendSubscription(Request $request, User $user)
    {
        $request->validate([
            'extend_months' => 'required|integer|min:1|max:12',
        ]);

        if (!in_array($user->subscription_plan, ['basic', 'pro'])) {
            return redirect()->back()
                ->with('error', 'Hanya plan Basic/Pro yang bisa diperpanjang.');
        }

        try {
            $extendMonths = (int) $request->extend_months;
            
            // If expired or null, start from now
            $baseDate = $user->subscription_ends_at && $user->subscription_ends_at->isFuture()
                ? $user->subscription_ends_at
                : now();

            $user->update([
                'subscription_ends_at' => $baseDate->copy()->addMonths($extendMonths),
            ]);

            $newEndDate = $user->subscription_ends_at->format('d M Y');

            return redirect()->back()
                ->with('success', "Subscription berhasil diperpanjang sampai {$newEndDate}!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
