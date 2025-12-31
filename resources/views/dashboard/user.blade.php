@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Selamat datang, ' . Auth::user()->name . '!')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #758bf1 0%, #4460bc 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Invoice Bulan Ini </div>
                        <span class="small">Sisa: {{ $stats['remaining_invoices'] }}</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ $stats['invoices_this_month'] }}</div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Total Omzet</div>
                        <span class="small">Invoice sudah dibayar</span>
                    </div>
                </div>
                <div class="stat-card-value">
                    @if ($stats['total_revenue'] >= 1000000)
                        {{ number_format($stats['total_revenue'] / 1000000, 1) }}jt
                    @else
                        {{ number_format($stats['total_revenue'] / 1000, 0) }}rb
                    @endif
                </div>
            </div>
        </div>

        {{-- <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-card-title">Belum Dibayar</div>
                <div class="stat-card-value">{{ $stats['unpaid_invoices'] }}</div>
                <div class="stat-card-description">
                    {{ formatRupiah($stats['unpaid_amount'], false) }}
                </div>
            </div>
        </div> --}}

        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #34b375 0%, #34e0d2 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Customer</div>
                        <span class="small">Total Cutomer</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ $stats['total_customers'] }}</div>

            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #20dae4 100%);">
                <div class="d-flex">
                    <div class="stat-card-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-card-title">Produk</div>
                        <span class="small">Total Produk</span>
                    </div>
                </div>
                <div class="stat-card-value">{{ $stats['total_products'] }}</div>

            </div>
        </div>
    </div>

    <!-- Subscription Warning -->
    @if (Auth::user()->subscription_ends_at && $stats['subscription_expires_in_days'] !== null)
        @php
            $daysLeft = $stats['subscription_expires_in_days'];
        @endphp
        @if ($daysLeft <= 3 && $daysLeft >= 0)
            <div class="alert alert-warning mb-3 mb-md-4" role="alert"
                style="border-radius: var(--radius); border: 1px solid hsl(38 92% 50% / 0.2); background: hsl(38 92% 50% / 0.1); color: hsl(38 92% 50%);">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <strong style="display: block; margin-bottom: 0.5rem;">
                            {{ Auth::user()->subscription_plan === 'trial' ? 'Trial' : 'Subscription' }} Anda akan berakhir
                            dalam {{ $daysLeft }} hari!
                        </strong>
                        <p style="margin-bottom: 0.75rem; font-size: 0.875rem;">
                            {{ Auth::user()->subscription_plan === 'trial' ? 'Upgrade sekarang untuk terus menggunakan semua fitur.' : 'Perpanjang subscription Anda.' }}
                        </p>
                        <a href="{{ route('subscription.index') }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-star-fill"></i>
                            {{ Auth::user()->subscription_plan === 'trial' ? 'Upgrade Sekarang' : 'Perpanjang' }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Overdue Invoices Alert -->
    @if ($overdueInvoices > 0)
        <div class="alert alert-danger mb-3 mb-md-4" role="alert"
            style="border-radius: var(--radius); border: 1px solid hsl(var(--destructive) / 0.2); background: hsl(var(--destructive) / 0.1); color: hsl(var(--destructive));">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 1.5rem;"></i>
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 0.5rem;">{{ $overdueInvoices }} invoice sudah jatuh
                        tempo!</strong>
                    <p style="margin-bottom: 0.75rem; font-size: 0.875rem;">Segera lakukan follow up ke customer Anda.</p>
                    <a href="{{ route('invoices.index', ['status' => 'unpaid']) }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-eye"></i> Lihat Invoice
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Upcoming Due Alert -->
    @if ($upcomingDueInvoices->count() > 0)
        <div class="p-3 mb-4"
            style="border-radius: var(--radius); border: 1px solid hsl(38 92% 50% / 0.2); background: hsl(38 92% 50% / 0.1); color: hsl(38 92% 50%);">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="bi bi-clock" style="font-size: 1.35rem;"></i>
                <p class="mt-1 mb-0"> <strong>{{ $upcomingDueInvoices->count() }} invoice akan jatuh tempo dalam 7 hari
                    </strong></p>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                    <p class="card-description d-none d-md-block">Aksi cepat untuk memulai</p>
                </div>
                <div class="card-body">
                    <div class="row g-2 g-md-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary w-100"
                                style="height: 100%; min-height: 70px; flex-direction: column; justify-content: center; padding: 0.75rem;">
                                <i class="bi bi-plus-circle" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                                <span style="font-size: 0.875rem;">Buat Invoice</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('customers.create') }}" class="btn btn-secondary w-100"
                                style="height: 100%; min-height: 70px; flex-direction: column; justify-content: center; padding: 0.75rem;">
                                <i class="bi bi-person-plus" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                                <span style="font-size: 0.875rem;">Tambah Customer</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('products.create') }}" class="btn btn-secondary w-100"
                                style="height: 100%; min-height: 70px; flex-direction: column; justify-content: center; padding: 0.75rem;">
                                <i class="bi bi-box-seam" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                                <span style="font-size: 0.875rem;">Tambah Produk</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('settings') }}" class="btn btn-secondary w-100"
                                style="height: 100%; min-height: 70px; flex-direction: column; justify-content: center; padding: 0.75rem;">
                                <i class="bi bi-gear" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                                <span style="font-size: 0.875rem;">Pengaturan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Revenue Chart -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Trend</h3>
                    <p class="card-description">6 bulan terakhir</p>
                </div>
                <div class="card-body">
                    <div style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
                        @if ($revenueByMonth->count() > 0)
                            <canvas id="revenueChart"></canvas>
                        @else
                            <div style="text-align: center; color: hsl(var(--muted-foreground));">
                                <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p style="margin-top: 1rem;">Belum ada data revenue</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Customers</h3>
                    <p class="card-description">By revenue</p>
                </div>
                <div class="card-body">
                    @if ($topCustomers->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach ($topCustomers as $item)
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div
                                        style="width: 2.5rem; height: 2.5rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                        {{ strtoupper(substr($item['customer']->name, 0, 1)) }}
                                    </div>
                                    <div style="flex: 1; overflow: hidden;">
                                        <div
                                            style="font-weight: 600; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $item['customer']->name }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                            {{ formatRupiah($item['total_revenue']) }}
                                        </div>
                                    </div>
                                    <div class="badge badge-primary">
                                        {{ $item['total_invoices'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 2rem; color: hsl(var(--muted-foreground));">
                            <i class="bi bi-trophy" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p style="margin-top: 0.5rem; font-size: 0.875rem;">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="row g-3 g-md-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Invoice Terbaru</h3>
                        <p class="card-description d-none d-md-block">5 invoice terakhir yang dibuat</p>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline btn-sm">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if (isset($recentInvoices) && $recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Customer</th>
                                        <th class="d-none d-md-table-cell">Tanggal</th>
                                        <th class="d-none d-lg-table-cell">Jatuh Tempo</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <span
                                                    style="font-weight: 600; font-family: monospace; font-size: 0.8125rem;">
                                                    {{ $invoice->invoice_number }}
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="d-none d-md-flex"
                                                        style="width: 2rem; height: 2rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                        {{ strtoupper(substr($invoice->customer->name, 0, 1)) }}
                                                    </div>
                                                    <span
                                                        style="font-size: 0.875rem;">{{ Str::limit($invoice->customer->name, 20) }}</span>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell" style="font-size: 0.8125rem;">
                                                {{ $invoice->invoice_date->format('d M Y') }}</td>
                                            <td class="d-none d-lg-table-cell" style="font-size: 0.8125rem;">
                                                {{ $invoice->due_date->format('d M Y') }}</td>
                                            <td>
                                                <strong
                                                    style="font-size: 0.875rem;">{{ formatRupiah($invoice->total) }}</strong>
                                            </td>
                                            <td>
                                                {!! getInvoiceStatusBadge($invoice->status) !!}
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.25rem;">
                                                    <a href="{{ route('invoices.show', $invoice) }}"
                                                        class="btn btn-outline btn-sm" title="Lihat"
                                                        style="padding: 0.25rem 0.5rem;">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form action="{{ route('invoices.send-whatsapp', $invoice) }}"
                                                        method="POST" class="d-none d-md-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Kirim WhatsApp" style="padding: 0.25rem 0.5rem;">
                                                            <i class="bi bi-whatsapp"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem 1rem; text-align: center;">
                            <i class="bi bi-inbox"
                                style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground)); font-size: 1.125rem;">
                                Belum
                                ada invoice</h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                Mulai buat invoice pertama Anda sekarang
                            </p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Buat Invoice Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tips & Info (Hidden on mobile) -->
    <div class="row g-3 g-md-4 mt-2 d-none d-md-flex">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div
                            style="width: 3rem; height: 3rem; background: hsl(var(--primary) / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-lightbulb" style="font-size: 1.5rem; color: hsl(var(--primary));"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Tips: Kirim Invoice via WhatsApp
                            </h4>
                            <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                Invoice Anda akan langsung dikirim ke WhatsApp customer dengan satu klik. Lebih
                                cepat dan
                                praktis!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div
                            style="width: 3rem; height: 3rem; background: hsl(142 76% 36% / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-shield-check" style="font-size: 1.5rem; color: hsl(142 76% 36%);"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Invoice Aman & Profesional</h4>
                            <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                Setiap invoice memiliki link unik yang aman. Customer dapat melihat dan download
                                invoice
                                kapan saja.
                            </p>
                        </div>
                    </div>
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
            @if ($revenueByMonth->count() > 0)
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
                    
                    console.log('Chart data:', { labels, data });

                    // Create chart
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue',
                                data: data,
                                borderColor: 'hsl(220, 90%, 56%)',
                                backgroundColor: 'hsla(220, 90%, 56%, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'hsl(220, 90%, 56%)',
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
                    
                    console.log('Chart created successfully!');
                } catch (error) {
                    console.error('Error creating chart:', error);
                }
            @else
                console.log('No revenue data to display');
            @endif
        });
    </script>
@endpush
