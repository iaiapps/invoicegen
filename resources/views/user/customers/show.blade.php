@extends('layouts.app')

@section('title', 'Detail Customer')
@section('page-title', 'Detail Customer')
@section('page-description', $customer->name)

@section('content')
    <!-- Customer Info -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem; flex-wrap: wrap;">
                        <!-- Avatar -->
                        <div
                            style="width: 5rem; height: 5rem; background: hsl(var(--primary)); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 2rem; flex-shrink: 0;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>

                        <!-- Info -->
                        <div style="flex: 1; min-width: 250px;">
                            <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600; ">
                                {{ $customer->name }}
                            </h2>

                            <div class="mt-2" style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                                <div>
                                    <div class="mb-1" style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); ">
                                        <i class="bi bi-telephone"></i> Telepon
                                    </div>
                                    <a href="https://wa.me/{{ formatPhone($customer->phone) }}" target="_blank"
                                        style="color: #25D366; text-decoration: none; font-weight: 500;">
                                        <i class="bi bi-whatsapp"></i> {{ $customer->phone }}
                                    </a>
                                </div>

                                @if ($customer->email)
                                    <div>
                                        <div class="mb-1"
                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); ">
                                            <i class="bi bi-envelope"></i> Email
                                        </div>
                                        <a href="mailto:{{ $customer->email }}"
                                            style="color: hsl(var(--foreground)); text-decoration: none; font-weight: 500;">
                                            {{ $customer->email }}
                                        </a>
                                    </div>
                                @endif

                                @if ($customer->address)
                                    <div>
                                        <div class="mb-1"
                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); ">
                                            <i class="bi bi-geo-alt"></i> Alamat
                                        </div>
                                        <div style="font-weight: 500;">
                                            {{ $customer->address }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($customer->notes)
                                <div
                                    style="margin-top: 1rem; padding: 1rem; background: hsl(var(--muted)); border-radius: var(--radius);">
                                    <div
                                        style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                        <i class="bi bi-sticky"></i> Catatan Internal
                                    </div>
                                    <div style="font-size: 0.875rem;">
                                        {{ $customer->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    {{-- <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-card-title">Total Invoice</div>
                <div class="stat-card-value">{{ $stats['total_invoices'] }}</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-card-title">Total Revenue</div>
                <div class="stat-card-value">
                    @if ($stats['total_revenue'] >= 1000000)
                        {{ number_format($stats['total_revenue'] / 1000000, 1) }}jt
                    @else
                        {{ number_format($stats['total_revenue'] / 1000, 0) }}rb
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-card-title">Belum Dibayar</div>
                <div class="stat-card-value">{{ $stats['unpaid_count'] }}</div>
                <div class="stat-card-description">
                    {{ formatRupiah($stats['unpaid_amount'], false) }}
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-card-title">Customer Sejak</div>
                <div class="stat-card-value" style="font-size: 1.25rem;">
                    {{ $customer->created_at->format('M Y') }}
                </div>
                <div class="stat-card-description">
                    {{ $customer->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Invoices List -->
    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Riwayat Invoice</h3>
                        <p class="card-description">Semua invoice untuk customer ini</p>
                    </div>
                    <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Invoice Baru
                    </a>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th class="d-none d-md-table-cell">Tanggal</th>
                                        <th class="d-none d-lg-table-cell">Jatuh Tempo</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <span
                                                    style="font-weight: 600; font-family: monospace; font-size: 0.8125rem;">
                                                    {{ $invoice->invoice_number }}
                                                </span>
                                            </td>
                                            <td class="d-none d-md-table-cell" style="font-size: 0.8125rem;">
                                                {{ $invoice->invoice_date->format('d M Y') }}
                                            </td>
                                            <td class="d-none d-lg-table-cell" style="font-size: 0.8125rem;">
                                                {{ $invoice->due_date->format('d M Y') }}
                                            </td>
                                            <td>
                                                <strong
                                                    style="font-size: 0.875rem;">{{ formatRupiah($invoice->total) }}</strong>
                                            </td>
                                            <td>
                                                {!! getInvoiceStatusBadge($invoice->status) !!}
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.25rem;">
                                                    <button class="btn btn-outline btn-sm" title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-success btn-sm" title="Kirim WhatsApp">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="padding: 1rem;">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div style="padding: 3rem 1rem; text-align: center;">
                            <i class="bi bi-inbox"
                                style="font-size: 4rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground));">Belum ada invoice</h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                Buat invoice pertama untuk customer ini
                            </p>
                            <a href="#" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Buat Invoice Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
