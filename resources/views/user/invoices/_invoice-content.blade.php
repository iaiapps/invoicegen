{{-- Invoice Content Template - Shared by show, public, and PDF --}}

<!-- Header -->
<div class="row mb-4 gy-4">
    <div class="col-12 col-md-6">
        @if (isset($isPdf) && $isPdf)
            @if ($invoice->user->shop_logo)
                <img src="{{ public_path('storage/' . $invoice->user->shop_logo) }}" alt="Logo"
                    style="max-height: 60px; margin-bottom: 1rem;">
            @endif
        @else
            @if ($invoice->user->shop_logo)
                <img src="{{ asset('storage/' . $invoice->user->shop_logo) }}" alt="Logo"
                    style="max-height: 60px; margin-bottom: 1rem;">
            @else
                <div
                    style="width: 60px; height: 60px; background: hsl(var(--primary)); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem; margin-bottom: 1rem;">
                    {{ strtoupper(substr($invoice->user->shop_name, 0, 2)) }}
                </div>
            @endif
        @endif
        <h3 style="margin: 0; font-weight: 700;">{{ $invoice->user->shop_name }}</h3>
        <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin-top: 0.5rem;">
            @if ($invoice->user->shop_address)
                <div>{{ $invoice->user->shop_address }}</div>
            @endif
            <div>{{ $invoice->user->shop_phone }}</div>
            <div>{{ $invoice->user->email }}</div>
        </div>
    </div>

    <div class="col-12 col-md-6 text-start text-md-end">
        <h2 style="margin: 0; font-weight: 700; font-size: 2rem;">INVOICE</h2>
        <div style="margin-top: 1rem; font-size: 0.875rem;">
            <div><strong>No:</strong> {{ $invoice->invoice_number }}</div>
            <div><strong>Tanggal:</strong> {{ $invoice->invoice_date->format('d M Y') }}</div>
            <div><strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
        </div>
    </div>
</div>

<!-- Customer Info -->
<div style="background: hsl(var(--muted)); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
    <div style="font-size: 0.875rem; font-weight: 600; color: hsl(var(--muted-foreground)); margin-bottom: 0.5rem;">
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
    <table class="table" style="width: 100%; ">
        <thead>
            <tr>
                <th style="background: #2c3e50; color: white;">Item</th>
                <th class="text-end" style="background: #2c3e50; color: white;">Qty</th>
                <th class="text-end" style="background: #2c3e50; color: white;">Harga</th>
                <th class="text-end" style="background: #2c3e50; color: white;">Total</th>
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
    <div style="min-width: 50%;">
        <div class="pe-2 pb-3"
            style="display: flex; justify-content: space-between; border-bottom: 1px solid hsl(var(--border));">
            <span>Subtotal:</span>
            <strong>{{ formatRupiah($invoice->subtotal) }}</strong>
        </div>
        @if ($invoice->tax_percentage > 0)
            <div class="pe-2 pb-3"
                style="display: flex; justify-content: space-between;  border-bottom: 1px solid hsl(var(--border));">
                <span>Pajak ({{ $invoice->tax_percentage }}%):</span>
                <strong>{{ formatRupiah($invoice->tax_amount) }}</strong>
            </div>
        @endif
        @if ($invoice->discount_amount > 0)
            <div class="pe-2 pb-3"
                style="display: flex; justify-content: space-between; border-bottom: 1px solid hsl(var(--border));">
                <span>Diskon:</span>
                <strong class="text-danger">-{{ formatRupiah($invoice->discount_amount) }}</strong>
            </div>
        @endif
        <div class="p-4 px-3 mt-3"
            style="display: flex; justify-content: space-between; background: #2c3e50; color: white;">
            <h4 class="fw-bold" style="margin: 0;">TOTAL :</h4>
            <h4 class="fw-bold" style="margin: 0;">{{ formatRupiah($invoice->total) }}</h4>
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
