@extends('layouts.app')

@section('title', 'Kelola Subscription')
@section('page-title', 'Kelola Subscription')
@section('page-description', 'Manajemen semua subscription & pembayaran')

@section('content')
    <!-- Stats Cards -->
    {{-- <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-card-title">Total Revenue</div>
                <div class="stat-card-value">{{ formatRupiah($stats['total_revenue'], false) }}</div>
                <div class="stat-card-description d-none d-md-block">
                    All time
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-card-title">Bulan Ini</div>
                <div class="stat-card-value">{{ formatRupiah($stats['revenue_this_month'], false) }}</div>
                <div class="stat-card-description">
                    {{ now()->format('F Y') }}
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-card-title">Pending</div>
                <div class="stat-card-value">{{ $stats['pending_payments'] }}</div>
                <div class="stat-card-description">
                    Belum dibayar
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="stat-card-title">Total</div>
                <div class="stat-card-value">{{ $stats['total_subscriptions'] }}</div>
                <div class="stat-card-description">
                    Subscriptions
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Filter & Search -->
    <div class="card mb-3 mb-md-4">
        <div class="card-body">
            <form action="{{ route('admin.subscriptions.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Cari user..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" name="plan">
                            <option value="">Semua Paket</option>
                            <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Subscription</h3>
            <p class="card-description">Total: {{ $subscriptions->total() }} subscription</p>
        </div>
        <div class="card-body" style="padding: 0;">
            @if ($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Period</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptions as $sub)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.875rem;">
                                            {{ $sub->user->shop_name ?? $sub->user->name }}</div>
                                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                            {{ $sub->user->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ getSubscriptionBadgeClass($sub->plan) }}">
                                            {{ ucfirst($sub->plan) }}
                                        </span>
                                        <div
                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-top: 0.25rem;">
                                            {{ $sub->invoice_limit }} invoice
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ formatRupiah($sub->amount) }}</strong>
                                        @if ($sub->payment_method)
                                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                {{ ucfirst(str_replace('_', ' ', $sub->payment_method)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.8125rem;">
                                        @if ($sub->starts_at && $sub->ends_at)
                                            <div>{{ $sub->starts_at->format('d M Y') }}</div>
                                            <div style="color: hsl(var(--muted-foreground));">
                                                {{ $sub->ends_at->format('d M Y') }}</div>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.8125rem;">
                                        @if ($sub->paid_at)
                                            <div style="font-weight: 600;">{{ $sub->paid_at->format('d M Y') }}</div>
                                            <div style="color: hsl(var(--muted-foreground));">
                                                {{ $sub->paid_at->format('H:i') }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($sub->payment_status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif ($sub->payment_status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif ($sub->payment_status === 'failed')
                                            <span class="badge badge-danger">Failed</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($sub->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $sub) }}"
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
                    {{ $subscriptions->links() }}
                </div>
            @else
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <i class="bi bi-credit-card"
                        style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                    <p style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                        @if (request()->hasAny(['search', 'plan', 'status']))
                            Tidak ada subscription yang sesuai dengan filter
                        @else
                            Belum ada subscription
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endpush
