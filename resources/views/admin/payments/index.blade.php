@extends('layouts.app')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-description', 'Monitoring semua transaksi pembayaran')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-card-title">Total Paid</div>
                <div class="stat-card-value">{{ formatRupiah($stats['total_paid'], false) }}</div>
                <div class="stat-card-description">
                    {{ $stats['paid_count'] }} transaksi
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-card-title">Pending</div>
                <div class="stat-card-value">{{ formatRupiah($stats['pending_amount'], false) }}</div>
                <div class="stat-card-description">
                    {{ $stats['pending_count'] }} transaksi
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div class="stat-card-title">Bulan Ini</div>
                <div class="stat-card-value">{{ formatRupiah($stats['paid_this_month'], false) }}</div>
                <div class="stat-card-description">
                    {{ now()->format('F Y') }}
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-card-title">Total Transaksi</div>
                <div class="stat-card-value">{{ $stats['total_transactions'] }}</div>
                <div class="stat-card-description">
                    All time
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card mb-3 mb-md-4">
        <div class="card-header">
            <h3 class="card-title">Revenue Trend</h3>
            <p class="card-description">6 bulan terakhir</p>
        </div>
        <div class="card-body">
            @if ($revenueByMonth->count() > 0)
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            @else
                <div style="padding: 3rem; text-align: center; color: hsl(var(--muted-foreground));">
                    <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p style="margin-top: 1rem;">Belum ada data revenue</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-3 mb-md-4">
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <input type="text" class="form-control" name="search"
                            placeholder="Cari user atau reference..." value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <input type="date" class="form-control" name="from" placeholder="Dari"
                            value="{{ request('from') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <input type="date" class="form-control" name="to" placeholder="Sampai"
                            value="{{ request('to') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                    @if (request()->hasAny(['search', 'status', 'from', 'to']))
                        <div class="col-12 col-md-1">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline" style="width: 100%;">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Pembayaran</h3>
            <p class="card-description">Total: {{ $payments->total() }} transaksi</p>
        </div>
        <div class="card-body" style="padding: 0;">
            @if ($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td style="font-family: monospace; font-weight: 600;">#{{ $payment->id }}</td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.875rem;">
                                            {{ $payment->user->shop_name ?? $payment->user->name }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                            {{ $payment->user->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ getSubscriptionBadgeClass($payment->plan) }}">
                                            {{ ucfirst($payment->plan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong style="font-size: 0.9375rem;">{{ formatRupiah($payment->amount) }}</strong>
                                    </td>
                                    <td>
                                        <div style="font-family: monospace; font-size: 0.8125rem;">
                                            {{ $payment->payment_reference ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="font-size: 0.8125rem;">
                                        {{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : '-' }}
                                    </td>
                                    <td>
                                        @if ($payment->payment_status === 'paid')
                                            <span class="badge badge-success">
                                                <i class="bi bi-check-circle"></i> Paid
                                            </span>
                                        @elseif ($payment->payment_status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @elseif ($payment->payment_status === 'failed')
                                            <span class="badge badge-danger">
                                                <i class="bi bi-x-circle"></i> Failed
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($payment->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.8125rem;">
                                        @if ($payment->paid_at)
                                            <div style="font-weight: 600;">{{ $payment->paid_at->format('d M Y') }}</div>
                                            <div style="color: hsl(var(--muted-foreground));">
                                                {{ $payment->paid_at->format('H:i') }}
                                            </div>
                                        @else
                                            <div style="color: hsl(var(--muted-foreground));">
                                                {{ $payment->created_at->format('d M Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $payment) }}"
                                            class="btn btn-sm btn-outline" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="padding: 1rem 1.5rem; border-top: 1px solid hsl(var(--border));">
                    {{ $payments->links() }}
                </div>
            @else
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <i class="bi bi-cash-stack"
                        style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                    <p style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                        @if (request()->hasAny(['search', 'status', 'from', 'to']))
                            Tidak ada pembayaran yang sesuai dengan filter
                        @else
                            Belum ada pembayaran
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        @if ($revenueByMonth->count() > 0)
            const ctx = document.getElementById('revenueChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($revenueByMonth->pluck('month')) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($revenueByMonth->pluck('total')) !!},
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgb(102, 126, 234)',
                        borderWidth: 1
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
                                },
                                afterLabel: function(context) {
                                    const count = {!! json_encode($revenueByMonth->pluck('count')) !!} [context.dataIndex];
                                    return count + ' transaksi';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                            }
                        }
                    }
                }
            });
        @endif
    </script>
@endpush
