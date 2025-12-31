@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'Overview semua data sistem')

@section('content')
    <!-- Admin Stats Cards -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <!-- Total Revenue -->
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #34b375 0%, #2d9960 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Total Revenue</div>
                        <span class="small">Subscription payments</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ formatRupiah($stats['total_revenue'] ?? '0', false) }}</div>
            </div>
        </div>

        <!-- Active Subscribers -->
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #4460bc 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Active Subscribers</div>
                        <span class="small">{{ $stats['basic_users'] }} Basic + {{ $stats['pro_users'] }} Pro</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ $stats['basic_users'] + $stats['pro_users'] }}</div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Pending Approvals</div>
                        <span class="small">Need action</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ $stats['pending_subscriptions'] ?? '0' }}</div>
            </div>
        </div>

        <!-- Revenue This Month -->
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Revenue This Month</div>
                        <span class="small">{{ now()->format('F Y') }}</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ formatRupiah($stats['revenue_this_month'] ?? '0', false) }}</div>
            </div>
        </div>
    </div>

    <!-- Subscription Stats Row -->
    <div class="row g-3 mb-3 mb-md-4">
        <div class="col-6 col-md-3">
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 0.875rem; color: hsl(215 16% 47%); margin-bottom: 0.5rem;">Free Users</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: hsl(215 16% 47%);">{{ $stats['free_users'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 0.875rem; color: hsl(220 90% 56%); margin-bottom: 0.5rem;">Basic Users</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: hsl(220 90% 56%);">{{ $stats['basic_users'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 0.875rem; color: hsl(261 51% 51%); margin-bottom: 0.5rem;">Pro Users</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: hsl(261 51% 51%);">{{ $stats['pro_users'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 0.875rem; color: hsl(38 92% 50%); margin-bottom: 0.5rem;">Grace Period</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: hsl(38 92% 50%);">
                        {{ $stats['grace_period_users'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Subscriptions Alert -->
    @if ($expiringSubscriptions->count() > 0)
        <div class="p-3 alert-warning mb-3 mb-md-4" role="alert"
            style="border-radius: var(--radius); border: 1px solid hsl(38 92% 50% / 0.2); background: hsl(38 92% 50% / 0.1); color: hsl(38 92% 50%);">
            <div style="display: flex; align-items: start; gap: 1rem;">
                {{-- <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5rem;"></i> --}}
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 0.5rem;">{{ $expiringSubscriptions->count() }}
                        subscription akan berakhir dalam 7 hari!</strong>
                    <p style="margin-bottom: 0; font-size: 0.875rem;">
                        Users:
                        @foreach ($expiringSubscriptions->take(3) as $user)
                            <strong>{{ $user->shop_name }}</strong>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                        @if ($expiringSubscriptions->count() > 3)
                            dan {{ $expiringSubscriptions->count() - 3 }} lainnya
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pending Subscriptions -->
    @if ($pendingSubscriptions->count() > 0)
        <div class="card mb-3 mb-md-4">
            <div class="card-header" style="background: hsl(220 90% 96%); border-bottom: 1px solid hsl(220 90% 80%);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h5 style="margin: 0; color: hsl(220 90% 56%);">
                        <i class="bi bi-clock-history"></i>
                        {{ $stats['pending_subscriptions'] }} Subscription Menunggu Approval
                    </h5>
                    <a href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}"
                        class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-sm" style="margin: 0;">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingSubscriptions as $sub)
                                <tr>
                                    <td>
                                        <strong>{{ $sub->user->name }}</strong><br>
                                        <small>{{ $sub->user->email }}</small>
                                    </td>
                                    <td><span
                                            class="badge badge-{{ $sub->plan === 'pro' ? 'primary' : 'info' }}">{{ ucfirst($sub->plan) }}</span>
                                    </td>
                                    <td><strong>Rp {{ number_format($sub->amount, 0, ',', '.') }}</strong></td>
                                    <td>{{ $sub->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $sub) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="me-2 bi bi-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 g-md-4">
        <!-- Subscription Revenue Chart -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-graph-up-arrow text-success"></i> Subscription Revenue Trend
                    </h3>
                    <p class="card-description">Payment success 6 bulan terakhir</p>
                </div>
                <div class="card-body">
                    <div style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        @if (($revenueByMonth ?? collect())->count() > 0)
                            <canvas id="revenueChart"></canvas>
                        @else
                            <div style="text-align: center; color: hsl(var(--muted-foreground));">
                                <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p style="margin-top: 1rem;">Belum ada subscription payment</p>
                                <small style="color: hsl(var(--muted-foreground)); opacity: 0.7;">Revenue akan muncul
                                    setelah ada payment yang di-approve</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriber Breakdown -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-pie-chart text-primary"></i> User Distribution
                    </h3>
                    <p class="card-description">By subscription plan</p>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <!-- Free Users -->
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: hsl(215 16% 97%); border-radius: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: hsl(215 16% 47%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem;">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">Free Plan</div>
                                    <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">30 invoice/bulan
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: hsl(215 16% 47%);">
                                    {{ $stats['free_users'] }}</div>
                                <div style="font-size: 0.7rem; color: hsl(var(--muted-foreground));">users</div>
                            </div>
                        </div>

                        <!-- Basic Users -->
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: hsl(220 90% 96%); border-radius: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: hsl(220 90% 56%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem;">
                                    <i class="bi bi-box"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">Basic Plan</div>
                                    <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">Rp 25K/bulan
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: hsl(220 90% 56%);">
                                    {{ $stats['basic_users'] }}</div>
                                <div style="font-size: 0.7rem; color: hsl(var(--muted-foreground));">users</div>
                            </div>
                        </div>

                        <!-- Pro Users -->
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: hsl(261 51% 96%); border-radius: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: hsl(261 51% 51%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem;">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">Pro Plan</div>
                                    <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">Rp 49K/bulan
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: hsl(261 51% 51%);">
                                    {{ $stats['pro_users'] }}</div>
                                <div style="font-size: 0.7rem; color: hsl(var(--muted-foreground));">users</div>
                            </div>
                        </div>

                        <!-- Grace Period -->
                        @if ($stats['grace_period_users'] > 0)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: hsl(38 92% 96%); border-radius: 0.5rem; border: 1px solid hsl(38 92% 80%);">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div
                                        style="width: 2.5rem; height: 2.5rem; background: hsl(38 92% 50%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem;">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem;">Grace Period</div>
                                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">7 hari
                                            sebelum downgrade</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.5rem; font-weight: 700; color: hsl(38 92% 50%);">
                                        {{ $stats['grace_period_users'] }}</div>
                                    <div style="font-size: 0.7rem; color: hsl(var(--muted-foreground));">users</div>
                                </div>
                            </div>
                        @endif

                        <!-- Summary -->
                        <div style="padding-top: 0.75rem; border-top: 1px solid hsl(var(--border));">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.875rem; font-weight: 600;">Total Users</span>
                                <span style="font-size: 1.25rem; font-weight: 700;">{{ $stats['total_users'] }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <span style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">Active
                                    Subscribers</span>
                                <span
                                    style="font-size: 1rem; font-weight: 600; color: hsl(142 76% 36%);">{{ $stats['basic_users'] + $stats['pro_users'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users & Invoices -->
    <div class="row g-3 g-md-4 mt-2">
        <!-- Recent Users -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 class="card-title">Recent Users</h3>
                        <p class="card-description">10 user terbaru</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                        Lihat Semua </a>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Joined</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="d-none d-md-flex"
                                                        style="width: 2rem; height: 2rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; font-size: 0.875rem;">
                                                            {{ $user->shop_name }}</div>
                                                        <div
                                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                            {{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ getSubscriptionBadgeClass($user->subscription_plan) }}">
                                                    {{ ucfirst($user->subscription_plan) }}
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8125rem;">
                                                {{ $user->created_at->diffForHumans() }}
                                            </td>
                                            <td>
                                                @if ($user->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center;">
                            <i class="bi bi-people"
                                style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <p style="margin-top: 1rem; color: hsl(var(--muted-foreground));">Belum ada user</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Invoices (All Users) -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 class="card-title">Recent Invoices</h3>
                        <p class="card-description">10 invoice terbaru</p>
                    </div>
                    {{-- <a href="#" class="btn btn-outline btn-sm">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a> --}}
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>User</th>
                                        <th class="d-none d-md-table-cell">Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <div
                                                    style="font-weight: 600; font-family: monospace; font-size: 0.8125rem;">
                                                    {{ $invoice->invoice_number }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                    {{ $invoice->customer->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.875rem; font-weight: 500;">
                                                    {{ $invoice->user->shop_name }}
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <strong style="font-size: 0.875rem;">
                                                    {{ formatRupiah($invoice->total) }}
                                                </strong>
                                            </td>
                                            <td>
                                                {!! getInvoiceStatusBadge($invoice->status) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center;">
                            <i class="bi bi-file-earmark-text"
                                style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <p style="margin-top: 1rem; color: hsl(var(--muted-foreground));">Belum ada invoice</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- bootstrap --}}
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (isset($revenueByMonth) && $revenueByMonth->count() > 0)
                try {
                    // Check if Chart.js is loaded
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js is not loaded!');
                        return;
                    }

                    // Get canvas element
                    const ctx = document.getElementById('revenueChart');
                    if (!ctx) {
                        console.error('Canvas element #revenueChart not found!');
                        return;
                    }

                    // Prepare data
                    const labels = {!! json_encode($revenueByMonth->pluck('month')) !!};
                    const data = {!! json_encode($revenueByMonth->pluck('total')) !!};

                    console.log('Admin Chart data:', {
                        labels,
                        data
                    });

                    // Create chart
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Subscription Revenue',
                                data: data,
                                borderColor: 'hsl(142, 76%, 36%)',
                                backgroundColor: 'hsla(142, 76%, 36%, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'hsl(142, 76%, 36%)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                            } else {
                                                return 'Rp ' + value;
                                            }
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    console.log('Admin chart created successfully!');
                } catch (error) {
                    console.error('Error creating admin chart:', error);
                }
            @else
                console.log('No revenue data to display in admin dashboard');
            @endif
        });
    </script>
@endpush
