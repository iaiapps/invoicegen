@extends('layouts.app')

@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')
@section('page-description', $product->name)

@section('content')
    <!-- Product Info -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem; flex-wrap: wrap;">
                        <!-- Icon -->
                        <div
                            style="width: 5rem; height: 5rem; background: hsl(var(--primary) / 0.1); border-radius: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-box-seam" style="font-size: 2.5rem; color: hsl(var(--primary));"></i>
                        </div>

                        <!-- Info -->
                        <div style="flex: 1; min-width: 250px;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0;">
                                    {{ $product->name }}
                                </h2>
                                @if ($product->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-top: 1rem;">
                                @if ($product->sku)
                                    <div>
                                        <div
                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                            <i class="bi bi-upc"></i> SKU
                                        </div>
                                        <div style="font-weight: 600; font-family: monospace;">
                                            {{ $product->sku }}
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <div
                                        style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                        <i class="bi bi-currency-dollar"></i> Harga
                                    </div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: hsl(var(--primary));">
                                        {{ formatRupiah($product->price) }}
                                    </div>
                                </div>
                            </div>

                            @if ($product->description)
                                <div
                                    style="margin-top: 1rem; padding: 1rem; background: hsl(var(--muted)); border-radius: var(--radius);">
                                    <div
                                        style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.5rem;">
                                        <i class="bi bi-text-paragraph"></i> Deskripsi
                                    </div>
                                    <div style="font-size: 0.875rem;">
                                        {{ $product->description }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                                <i class="me-2 bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('products.toggle-status', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary">
                                    <i class="me-2 bi bi-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <a href="{{ route('products.index') }}" class="btn btn-outline">
                                <i class="me-2 bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    {{-- <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-6 col-lg-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-card-title">Total Terjual</div>
                <div class="stat-card-value">{{ number_format($stats['total_sold'], 0, ',', '.') }}</div>
                <div class="stat-card-description">Unit</div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
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

        <div class="col-12 col-lg-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-card-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-card-title">Digunakan</div>
                <div class="stat-card-value">{{ $stats['times_used'] }}</div>
                <div class="stat-card-description">Kali dalam invoice</div>
            </div>
        </div>
    </div> --}}

    <!-- Usage History -->
    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Riwayat Penggunaan</h3>
                        <p class="card-description">Produk ini digunakan dalam invoice berikut</p>
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if ($invoiceItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Customer</th>
                                        <th class="d-none d-md-table-cell">Tanggal</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoiceItems as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoices.show', $item->invoice) }}"
                                                    style="font-weight: 600; font-family: monospace; font-size: 0.8125rem; text-decoration: none;">
                                                    {{ $item->invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.875rem;">
                                                    {{ $item->invoice->customer->name }}
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell" style="font-size: 0.8125rem;">
                                                {{ $item->invoice->invoice_date->format('d M Y') }}
                                            </td>
                                            <td>
                                                <span
                                                    style="font-weight: 600;">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                                            </td>
                                            <td style="font-size: 0.875rem;">
                                                {{ formatRupiah($item->price) }}
                                            </td>
                                            <td>
                                                <strong
                                                    style="font-size: 0.875rem;">{{ formatRupiah($item->total) }}</strong>
                                            </td>
                                            <td>
                                                {!! getInvoiceStatusBadge($item->invoice->status) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="padding: 1rem;">
                            {{ $invoiceItems->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div style="padding: 3rem 1rem; text-align: center;">
                            <i class="bi bi-inbox"
                                style="font-size: 4rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground));">Belum digunakan</h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                Produk ini belum pernah digunakan dalam invoice
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
