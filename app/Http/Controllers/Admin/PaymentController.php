<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by user or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('shop_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(20);

        // Stats
        $stats = [
            'total_paid' => Subscription::where('payment_status', 'paid')->sum('amount'),
            'pending_amount' => Subscription::where('payment_status', 'pending')->sum('amount'),
            'paid_this_month' => Subscription::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'total_transactions' => Subscription::count(),
            'paid_count' => Subscription::where('payment_status', 'paid')->count(),
            'pending_count' => Subscription::where('payment_status', 'pending')->count(),
        ];

        // Revenue by month (last 6 months)
        $revenueByMonth = Subscription::where('payment_status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.payments.index', compact('payments', 'stats', 'revenueByMonth'));
    }
}
