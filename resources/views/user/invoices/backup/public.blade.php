<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: 222.2 47.4% 11.2%;
            --border: 214.3 31.8% 91.4%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(222.2 47.4% 20%) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .invoice-body {
            padding: 2rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-paid {
            background: #10b981;
            color: white;
        }

        .status-unpaid {
            background: #f59e0b;
            color: white;
        }

        .status-cancelled {
            background: #ef4444;
            color: white;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .invoice-container {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1 style="margin: 0; font-size: 2rem;">Invoice</h1>
            <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">{{ $invoice->user->shop_name }}</p>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- Status & Actions -->
            <div style="text-align: center; margin-bottom: 2rem;" class="no-print">
                @if ($invoice->status === 'paid')
                    <span class="status-badge status-paid">
                        <i class="bi bi-check-circle"></i> LUNAS
                    </span>
                @elseif($invoice->status === 'unpaid')
                    <span class="status-badge status-unpaid">
                        <i class="bi bi-clock"></i> BELUM DIBAYAR
                    </span>
                @else
                    <span class="status-badge status-cancelled">
                        <i class="bi bi-x-circle"></i> DIBATALKAN
                    </span>
                @endif

                <div style="margin-top: 1rem;">
                    <a href="{{ route('invoice.pdf', $invoice->unique_id) }}" class="btn btn-primary"
                        style="margin-right: 0.5rem;">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>

            <!-- Invoice Info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <h5 style="font-weight: 700; margin-bottom: 1rem;">Dari:</h5>
                    <div style="font-weight: 700;">{{ $invoice->user->shop_name }}</div>
                    @if ($invoice->user->shop_address)
                        <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                            {{ $invoice->user->shop_address }}</div>
                    @endif
                    <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                        {{ $invoice->user->shop_phone }}</div>
                    <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">{{ $invoice->user->email }}
                    </div>
                </div>

                <div>
                    <h5 style="font-weight: 700; margin-bottom: 1rem;">Kepada:</h5>
                    <div style="font-weight: 700;">{{ $invoice->customer->name }}</div>
                    <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                        {{ $invoice->customer->phone }}</div>
                    @if ($invoice->customer->email)
                        <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                            {{ $invoice->customer->email }}</div>
                    @endif
                    @if ($invoice->customer->address)
                        <div style="color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                            {{ $invoice->customer->address }}</div>
                    @endif
                </div>
            </div>

            <div
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; padding: 1rem; background: hsl(var(--muted)); border-radius: 0.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">No Invoice</div>
                    <div style="font-weight: 700;">{{ $invoice->invoice_number }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">Tanggal</div>
                    <div style="font-weight: 700;">{{ $invoice->invoice_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">Jatuh Tempo</div>
                    <div style="font-weight: 700;">{{ $invoice->due_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">Total</div>
                    <div style="font-weight: 700; color: hsl(var(--primary)); font-size: 1.25rem;">
                        {{ formatRupiah($invoice->total) }}</div>
                </div>
            </div>

            <!-- Items -->
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
                                        {{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-end">{{ formatRupiah($item->price) }}</td>
                            <td class="text-end"><strong>{{ formatRupiah($item->total) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                <div style="min-width: 350px;">
                    <div
                        style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-top: 1px solid hsl(var(--border));">
                        <span>Subtotal:</span>
                        <strong>{{ formatRupiah($invoice->subtotal) }}</strong>
                    </div>
                    @if ($invoice->tax_percentage > 0)
                        <div
                            style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-top: 1px solid hsl(var(--border));">
                            <span>Pajak ({{ $invoice->tax_percentage }}%):</span>
                            <strong>{{ formatRupiah($invoice->tax_amount) }}</strong>
                        </div>
                    @endif
                    @if ($invoice->discount_amount > 0)
                        <div
                            style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-top: 1px solid hsl(var(--border));">
                            <span>Diskon:</span>
                            <strong style="color: #ef4444;">-{{ formatRupiah($invoice->discount_amount) }}</strong>
                        </div>
                    @endif
                    <div
                        style="display: flex; justify-content: space-between; padding: 1rem 0; border-top: 2px solid hsl(var(--primary)); background: hsl(var(--primary) / 0.05); margin: 0 -1rem; padding-left: 1rem; padding-right: 1rem;">
                        <h4 style="margin: 0;">TOTAL:</h4>
                        <h4 style="margin: 0; color: hsl(var(--primary));">{{ formatRupiah($invoice->total) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if ($invoice->notes)
                <div style="margin-top: 2rem; padding: 1rem; background: hsl(var(--muted)); border-radius: 0.5rem;">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Catatan:</div>
                    <div style="font-size: 0.875rem;">{{ $invoice->notes }}</div>
                </div>
            @endif

            <!-- Footer -->
            <div
                style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid hsl(var(--border)); text-align: center; color: hsl(var(--muted-foreground)); font-size: 0.875rem;">
                <p>Terima kasih atas kepercayaan Anda!</p>
                <p style="margin: 0;">Invoice ini dibuat oleh <strong>{{ $invoice->user->shop_name }}</strong></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
