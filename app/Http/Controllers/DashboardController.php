<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Check if admin
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        // Regular user dashboard
        return $this->userDashboard();
    }

    /**
     * Admin Dashboard (Platform Statistics)
     */
    private function adminDashboard()
    {
        // Overall Platform Statistics
        $stats = [
            'total_users' => User::where('id', '!=', Auth::user()->id)->count(),
            'free_users' => User::where('id', '!=', Auth::user()->id)->where('subscription_plan', 'free')->count(),
            'basic_users' => User::where('subscription_plan', 'basic')
                ->where(function ($q) {
                    $q->whereNull('subscription_ends_at')
                        ->orWhere('subscription_ends_at', '>', now()->subDays(7)); // Include grace period
                })->count(),
            'pro_users' => User::where('subscription_plan', 'pro')
                ->where(function ($q) {
                    $q->whereNull('subscription_ends_at')
                        ->orWhere('subscription_ends_at', '>', now()->subDays(7)); // Include grace period
                })->count(),
            'grace_period_users' => User::whereIn('subscription_plan', ['basic', 'pro'])
                ->where('subscription_ends_at', '<', now())
                ->where('subscription_ends_at', '>', now()->subDays(7))
                ->count(),
            'pending_subscriptions' => Subscription::where('payment_status', 'pending')->count(),
            'total_revenue' => Subscription::where('payment_status', 'paid')->sum('amount') ?? 0,
            'revenue_this_month' => Subscription::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount') ?? 0,
            'total_invoices' => Invoice::count(),
            'pending_invoices' => Invoice::where('status', 'unpaid')->count(),
        ];

        // Revenue by month (last 6 months) from subscriptions
        $revenueByMonth = Subscription::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Pending subscriptions (need approval)
        $pendingSubscriptions = Subscription::where('payment_status', 'pending')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Top users by invoice count
        $topUsers = User::where('id', '!=', Auth::user()->id)
            ->withCount('invoices')
            ->with(['invoices' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->having('invoices_count', '>', 0)
            ->orderBy('invoices_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user,
                    'total_invoices' => $user->invoices_count,
                    'total_revenue' => $user->invoices->sum('total'),
                    'plan' => $user->subscription_plan,
                ];
            });

        // Recent users (last 10)
        $recentUsers = User::where('id', '!=', Auth::user()->id)
            ->with('roles')
            ->latest()
            ->take(10)
            ->get();

        // Subscription expiring soon (next 7 days)
        $expiringSubscriptions = User::whereIn('subscription_plan', ['basic', 'pro'])
            ->whereBetween('subscription_ends_at', [now(), now()->addDays(7)])
            ->orderBy('subscription_ends_at')
            ->get();

        // Users by subscription plan
        $usersByPlan = [
            'free' => User::where('subscription_plan', 'free')->count(),
            'basic' => User::where('subscription_plan', 'basic')->count(),
            'pro' => User::where('subscription_plan', 'pro')->count(),
        ];

        // New users this month
        $newUsersThisMonth = User::where('id', '!=', Auth::user()->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // User growth by month (last 6 months)
        $userGrowth = User::where('id', '!=', Auth::user()->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent subscription payments (last 10)
        $recentPayments = Subscription::with('user')
            ->whereIn('payment_status', ['paid', 'pending'])
            ->latest()
            ->take(10)
            ->get();

        // Recent invoices (all users - last 10)
        $recentInvoices = Invoice::with(['customer', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Platform activity stats
        $platformStats = [
            'total_invoices_created' => Invoice::count(),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'avg_invoices_per_user' => Invoice::count() > 0 ? round(Invoice::count() / max(1, User::where('id', '!=', Auth::user()->id)->count()), 1) : 0,
        ];

        return view('dashboard.admin', compact(
            'stats',
            'revenueByMonth',
            'topUsers',
            'recentUsers',
            'expiringSubscriptions',
            'usersByPlan',
            'newUsersThisMonth',
            'userGrowth',
            'recentPayments',
            'recentInvoices',
            'platformStats',
            'pendingSubscriptions'
        ));
    }

    /**
     * User Dashboard
     */
    private function userDashboard()
    {
        $user = Auth::user();

        // User Statistics
        $stats = [
            'total_invoices' => Invoice::where('user_id', $user->id)->count(),
            'total_revenue' => Invoice::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('total') ?? 0,
            'unpaid_invoices' => Invoice::where('user_id', $user->id)
                ->where('status', 'unpaid')
                ->count(),
            'unpaid_amount' => Invoice::where('user_id', $user->id)
                ->where('status', 'unpaid')
                ->sum('total') ?? 0,
            'total_customers' => Customer::where('user_id', $user->id)->count(),
            'total_products' => Product::where('user_id', $user->id)->count(),
            'invoices_this_month' => $user->invoice_count_this_month,
            'remaining_invoices' => getRemainingInvoices(),
            'subscription_expires_in_days' => $user->subscription_ends_at ? now()->diffInDays($user->subscription_ends_at, false) : null,
        ];

        // Revenue by month (last 6 months) - User only
        $revenueByMonth = Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top customers by revenue
        $topCustomers = Customer::where('user_id', $user->id)
            ->withCount('invoices')
            ->with(['invoices' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->get()
            ->map(function ($customer) {
                return [
                    'customer' => $customer,
                    'total_revenue' => $customer->invoices->sum('total'),
                    'total_invoices' => $customer->invoices_count,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(5);

        // Recent invoices (user only)
        $recentInvoices = Invoice::with('customer')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Overdue invoices
        $overdueInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->count();

        // Upcoming due invoices (next 7 days)
        $upcomingDueInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->get();

        return view('dashboard.user', compact(
            'stats',
            'revenueByMonth',
            'topCustomers',
            'recentInvoices',
            'overdueInvoices',
            'upcomingDueInvoices'
        ));
    }
}
