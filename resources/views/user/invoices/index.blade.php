@extends('layouts.app')

@section('title', 'Daftar Invoice')
@section('page-title', 'Invoice')
@section('page-description', 'Kelola semua invoice Anda')

@section('content')
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12">
            <!-- Quota Alert -->
            @if (Auth::user()->invoice_count_this_month >= Auth::user()->invoice_limit)
                <div class="alert alert-danger" role="alert"
                    style="border-radius: var(--radius); border: 1px solid hsl(var(--destructive) / 0.2); background: hsl(var(--destructive) / 0.1); color: hsl(var(--destructive));">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <i class="bi bi-exclamation-circle-fill" style="font-size: 1.5rem;"></i>
                        <div style="flex: 1;">
                            <strong>Limit invoice bulan ini sudah tercapai!</strong>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                                Upgrade paket Anda untuk membuat lebih banyak invoice.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif(getRemainingInvoices() <= 3)
                <div class="alert alert-warning" role="alert"
                    style="border-radius: var(--radius); border: 1px solid hsl(38 92% 50% / 0.2); background: hsl(38 92% 50% / 0.1); color: hsl(38 92% 50%);">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Sisa {{ getRemainingInvoices() }} invoice lagi bulan ini!</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Daftar Invoice</h3>
                        <p class="card-description d-none d-md-block">
                            Total: {{ $invoices->total() }} invoice |
                            Bulan ini: {{ Auth::user()->invoice_count_this_month }}/{{ Auth::user()->invoice_limit }}
                        </p>
                    </div>
                    @if (canCreateInvoice())
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="me-2 bi bi-plus-circle"></i> Buat Invoice
                        </a>
                    @else
                        <a href="{{ route('subscription.index') }}" class="btn btn-warning">
                            <i class="me-2 bi bi-star-fill"></i> Upgrade Paket
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('invoices.index') }}" class="mb-3 mb-md-4">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="🔍 Cari invoice atau customer..." value="{{ request('search') }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar
                                    </option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas
                                    </option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="sort_by" class="form-control">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                        Tanggal Buat</option>
                                    <option value="due_date" {{ request('sort_by') == 'due_date' ? 'selected' : '' }}>Jatuh
                                        Tempo</option>
                                    <option value="total" {{ request('sort_by') == 'total' ? 'selected' : '' }}>Total
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> <span class="d-none d-md-inline">Cari</span>
                                    </button>
                                    <a href="{{ route('invoices.index') }}" class="btn btn-outline">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    @if ($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Customer</th>
                                        <th>Tanggal</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}"
                                                    style="font-weight: 600; font-family: monospace; font-size: 0.8125rem; text-decoration: none;">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="d-none d-md-flex"
                                                        style="width: 2rem; height: 2rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                        {{ strtoupper(substr($invoice->customer->name, 0, 1)) }}
                                                    </div>
                                                    <span
                                                        style="font-size: 0.875rem;">{{ Str::limit($invoice->customer->name, 25) }}</span>
                                                </div>
                                            </td>
                                            <td class="" style="font-size: 0.8125rem;">
                                                {{ $invoice->invoice_date->format('d M Y') }}
                                            </td>
                                            <td class="" style="font-size: 0.8125rem;">
                                                <span
                                                    class="{{ $invoice->status == 'unpaid' && $invoice->due_date->isPast() ? 'text-danger' : '' }}">
                                                    {{ $invoice->due_date->format('d M Y') }}
                                                    @if ($invoice->status == 'unpaid' && $invoice->due_date->isPast())
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <strong
                                                    style="font-size: 0.875rem;">{{ formatRupiah($invoice->total) }}</strong>
                                            </td>
                                            <td>
                                                {!! getInvoiceStatusBadge($invoice->status) !!}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline btn-sm" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('invoices.show', $invoice) }}">
                                                                <i class="bi bi-eye"></i> Lihat Detail
                                                            </a>
                                                        </li>
                                                        @if ($invoice->status == 'unpaid')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('invoices.edit', $invoice) }}">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('invoice.public', $invoice->unique_id) }}"
                                                                target="_blank">
                                                                <i class="bi bi-box-arrow-up-right"></i> Buka Link Public
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('invoices.send-whatsapp', $invoice) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="bi bi-whatsapp"></i> Kirim ke WhatsApp
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 1.5rem;">
                            {{ $invoices->withQueryString()->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div style="padding: 3rem 1rem; text-align: center;">
                            <i class="bi bi-file-earmark-text"
                                style="font-size: 4rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                                @if (request('search') || request('status'))
                                    Tidak ada invoice yang ditemukan
                                @else
                                    Belum ada invoice
                                @endif
                            </h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                @if (request('search') || request('status'))
                                    Coba ubah filter pencarian Anda
                                @else
                                    Mulai buat invoice pertama Anda sekarang
                                @endif
                            </p>
                            @if (canCreateInvoice() && !request('search') && !request('status'))
                                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Buat Invoice Baru
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
