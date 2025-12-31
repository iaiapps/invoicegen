@extends('layouts.app')

@section('title', 'Detail Invoice')
@section('page-title', 'Invoice ' . $invoice->invoice_number)
@section('page-description', $invoice->customer->name)

@section('content')
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <div class="col-12">
            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">
                                {{ $invoice->invoice_number }}
                            </h2>
                            <div>
                                {!! getInvoiceStatusBadge($invoice->status) !!}
                                @if ($invoice->status == 'unpaid' && $invoice->due_date->isPast())
                                    <span class="badge badge-danger">
                                        <i class="me-2 bi bi-exclamation-circle"></i> Jatuh Tempo
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            @if ($invoice->status == 'unpaid')
                                <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Tandai invoice sebagai LUNAS?')">
                                        <i class="me-2 bi bi-check-circle"></i> Tandai Lunas
                                    </button>
                                </form>

                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">
                                    <i class="me-2 bi bi-pencil"></i> Edit
                                </a>
                            @endif

                            @if ($invoice->status == 'paid')
                                <form action="{{ route('invoices.mark-unpaid', $invoice) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Tandai invoice sebagai BELUM BAYAR?')">
                                        <i class="me-2 bi bi-arrow-counterclockwise"></i> Mark Unpaid
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('invoices.send-whatsapp', $invoice) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="me-2 bi bi-whatsapp"></i> Kirim WA
                                </button>
                            </form>

                            <a href="{{ route('invoice.pdf', $invoice->unique_id) }}" class="btn btn-outline"
                                target="_blank">
                                <i class="me-2 bi bi-download"></i> PDF
                            </a>

                            <a href="{{ route('invoice.public', $invoice->unique_id) }}" class="btn btn-outline"
                                target="_blank">
                                <i class="me-2 bi bi-box-arrow-up-right"></i> Public
                                Link
                            </a>

                            @if ($invoice->status == 'unpaid')
                                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Batalkan invoice ini?')">
                                        <i class="me-2 bi bi-x-circle"></i> Batalkan
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('invoices.index') }}" class="btn btn-outline">
                                <i class="me-2 bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Preview -->
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body" style="padding: 2rem;">
                    <!-- Header -->
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 2rem;">
                        <div>
                            @if (Auth::user()->shop_logo)
                                <img src="{{ asset('storage/' . Auth::user()->shop_logo) }}" alt="Logo"
                                    style="max-height: 60px; margin-bottom: 1rem;">
                            @else
                                <div
                                    style="width: 60px; height: 60px; background: hsl(var(--primary)); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem; margin-bottom: 1rem;">
                                    {{ strtoupper(substr(Auth::user()->shop_name, 0, 2)) }}
                                </div>
                            @endif
                            <h3 style="margin: 0; font-weight: 700;">{{ Auth::user()->shop_name }}</h3>
                            <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin-top: 0.5rem;">
                                @if (Auth::user()->shop_address)
                                    <div>{{ Auth::user()->shop_address }}</div>
                                @endif
                                <div>{{ Auth::user()->shop_phone }}</div>
                                <div>{{ Auth::user()->email }}</div>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <h2 style="margin: 0; font-weight: 700; font-size: 2rem;">INVOICE</h2>
                            <div style="margin-top: 1rem; font-size: 0.875rem;">
                                <div><strong>No:</strong> {{ $invoice->invoice_number }}</div>
                                <div><strong>Tanggal:</strong> {{ $invoice->invoice_date->format('d M Y') }}</div>
                                <div><strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div
                        style="background: hsl(var(--muted)); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
                        <div
                            style="font-size: 0.875rem; font-weight: 600; color: hsl(var(--muted-foreground)); margin-bottom: 0.5rem;">
                            Kepada:
                        </div>
                        <h4 style="margin: 0 0 0.5rem 0; font-weight: 600;">{{ $invoice->customer->name }}</h4>
                        <div style="font-size: 0.875rem;">
                            <div>{{ $invoice->customer->phone }}</div>
                            @if ($invoice->customer->email)
                                <div>{{ $invoice->customer->email }}</div>
                            @endif
                            @if ($invoice->customer->address)
                                <div>{{ $invoice->customer->address }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive" style="margin-bottom: 2rem;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ $item->product_name }}</div>
                                            @if ($item->description)
                                                <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">
                                                    {{ $item->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">{{ formatRupiah($item->price) }}</td>
                                        <td class="text-end"><strong>{{ formatRupiah($item->total) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 2rem;">
                        <div style="min-width: 300px;">
                            <div
                                style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid hsl(var(--border));">
                                <span>Subtotal:</span>
                                <strong>{{ formatRupiah($invoice->subtotal) }}</strong>
                            </div>
                            @if ($invoice->tax_percentage > 0)
                                <div
                                    style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid hsl(var(--border));">
                                    <span>Pajak ({{ $invoice->tax_percentage }}%):</span>
                                    <strong>{{ formatRupiah($invoice->tax_amount) }}</strong>
                                </div>
                            @endif
                            @if ($invoice->discount_amount > 0)
                                <div
                                    style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid hsl(var(--border));">
                                    <span>Diskon:</span>
                                    <strong class="text-danger">-{{ formatRupiah($invoice->discount_amount) }}</strong>
                                </div>
                            @endif
                            <div
                                style="display: flex; justify-content: space-between; padding: 1rem 0; background: hsl(var(--primary) / 0.1); margin: 1rem -1rem 0 -1rem; padding-left: 1rem; padding-right: 1rem; border-radius: var(--radius);">
                                <h4 style="margin: 0;">TOTAL:</h4>
                                <h4 style="margin: 0; color: hsl(var(--primary));">{{ formatRupiah($invoice->total) }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($invoice->notes)
                        <div style="background: hsl(var(--muted)); padding: 1rem; border-radius: var(--radius);">
                            <div style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Catatan:</div>
                            <div style="font-size: 0.875rem;">{{ $invoice->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Info Invoice</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Status</div>
                            <div>{!! getInvoiceStatusBadge($invoice->status) !!}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Dibuat</div>
                            <div style="font-weight: 600;">{{ $invoice->created_at->format('d M Y H:i') }}</div>
                        </div>

                        @if ($invoice->sent_at)
                            <div>
                                <div
                                    style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                    Terkirim</div>
                                <div style="font-weight: 600;">{{ $invoice->sent_at->format('d M Y H:i') }}</div>
                            </div>
                        @endif

                        @if ($invoice->paid_at)
                            <div>
                                <div
                                    style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                    Dibayar</div>
                                <div style="font-weight: 600;">{{ $invoice->paid_at->format('d M Y H:i') }}</div>
                            </div>
                        @endif

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Link Public</div>
                            <input type="text" class="form-control"
                                value="{{ route('invoice.public', $invoice->unique_id) }}" readonly
                                onclick="this.select()" style="font-size: 0.75rem;">
                            <button class="btn btn-outline btn-sm w-100 mt-2" onclick="copyToClipboard()">
                                <i class="me-2 bi bi-clipboard"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if ($invoice->status != 'paid')
                <div class="card">
                    <div class="card-body">
                        <div style="display: flex; align-items: start; gap: 1rem;">
                            <div
                                style="width: 3rem; height: 3rem; background: hsl(var(--primary) / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-lightbulb" style="font-size: 1.5rem; color: hsl(var(--primary));"></i>
                            </div>
                            <div>
                                <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Tips</h5>
                                <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                    Kirim invoice via WhatsApp agar customer langsung menerima notifikasi dan bisa langsung
                                    melihat invoice.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard() {
            const input = document.querySelector('input[readonly]');
            input.select();
            document.execCommand('copy');
            alert('Link berhasil dicopy!');
        }
    </script>
@endpush
