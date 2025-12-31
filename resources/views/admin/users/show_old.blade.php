@extends('layouts.app')

@section('title', 'Detail User - ' . $user->name)
@section('page-title', 'Detail User')
@section('page-description', $user->shop_name ?? $user->name)

@section('content')
    <div class="mb-3 mb-md-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-3 g-md-4">
        <!-- User Info -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi User</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        @if ($user->shop_logo)
                            <img src="{{ asset('storage/' . $user->shop_logo) }}" alt="Logo"
                                style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid hsl(var(--border));">
                        @else
                            <div
                                style="width: 100px; height: 100px; margin: 0 auto; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 600;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Nama Pemilik
                            </div>
                            <div style="font-weight: 600;">{{ $user->name }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Email
                            </div>
                            <div>{{ $user->email }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Nama Toko
                            </div>
                            <div style="font-weight: 600;">{{ $user->shop_name ?? '-' }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Alamat
                            </div>
                            <div>{{ $user->shop_address ?? '-' }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Telepon
                            </div>
                            <div>{{ $user->shop_phone ?? '-' }}</div>
                        </div>

                        <div class="divider"></div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Status
                            </div>
                            @if ($user->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Bergabung
                            </div>
                            <div>{{ $user->created_at->format('d M Y') }}</div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                {{ $user->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="divider"></div>

                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="btn {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                style="width: 100%;"
                                onclick="return confirm('Yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} user ini?')">
                                <i class="bi {{ $user->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Subscription Info -->
            <div class="card mt-3 mt-md-4">
                <div class="card-header">
                    <h3 class="card-title">Subscription</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Paket
                            </div>
                            <span class="badge {{ getSubscriptionBadgeClass($user->subscription_plan) }}"
                                style="font-size: 0.875rem;">
                                {{ ucfirst($user->subscription_plan) }}
                            </span>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Limit Invoice
                            </div>
                            <div style="font-weight: 600;">{{ $user->invoice_limit }} invoice/bulan</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Invoice Bulan Ini
                            </div>
                            <div style="font-weight: 600;">{{ $user->invoice_count_this_month }}/{{ $user->invoice_limit }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Berakhir Pada
                            </div>
                            <div style="font-weight: 600;">
                                {{ $user->subscription_ends_at ? $user->subscription_ends_at->format('d M Y') : '-' }}
                            </div>
                            @if ($user->subscription_ends_at)
                                <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                    {{ $user->subscription_ends_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats & Activities -->
        <div class="col-12 col-lg-8">
            <!-- Stats Cards -->
            <div class="row g-3 mb-3 mb-md-4">
                <div class="col-6 col-md-4">
                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="card-body" style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total_invoices'] }}</div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Total Invoice</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                        <div class="card-body" style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['paid_invoices'] }}</div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Lunas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <div class="card-body" style="text-align: center;">
                            <div style="font-size: 1.25rem; font-weight: 700;">{{ formatRupiah($stats['total_revenue'], false) }}</div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Total Omzet</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                        <div class="card-body" style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total_customers'] }}</div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Customer</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                        <div class="card-body" style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total_products'] }}</div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Produk</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="card mb-3 mb-md-4">
                <div class="card-header">
                    <h3 class="card-title">Invoice Terbaru</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInvoices as $invoice)
                                        <tr>
                                            <td style="font-family: monospace; font-weight: 600; font-size: 0.8125rem;">
                                                {{ $invoice->invoice_number }}
                                            </td>
                                            <td>{{ $invoice->customer->name }}</td>
                                            <td><strong>{{ formatRupiah($invoice->total) }}</strong></td>
                                            <td>{!! getInvoiceStatusBadge($invoice->status) !!}</td>
                                            <td style="font-size: 0.8125rem;">{{ $invoice->created_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: hsl(var(--muted-foreground));">
                            Belum ada invoice
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Subscriptions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Subscription</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($recentSubscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Period</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSubscriptions as $sub)
                                        <tr>
                                            <td>
                                                <span class="badge {{ getSubscriptionBadgeClass($sub->plan) }}">
                                                    {{ ucfirst($sub->plan) }}
                                                </span>
                                            </td>
                                            <td><strong>{{ formatRupiah($sub->amount) }}</strong></td>
                                            <td>
                                                @if ($sub->payment_status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif ($sub->payment_status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-danger">{{ ucfirst($sub->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td style="font-size: 0.8125rem;">
                                                {{ $sub->starts_at->format('d M') }} - {{ $sub->ends_at->format('d M Y') }}
                                            </td>
                                            <td style="font-size: 0.8125rem;">
                                                {{ $sub->paid_at ? $sub->paid_at->format('d M Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: hsl(var(--muted-foreground));">
                            Belum ada riwayat subscription
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endpush
