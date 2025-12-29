<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }

        .header-flex {
            display: table;
            width: 100%;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 50%;
        }

        .header-right {
            width: 50%;
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-flex {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table thead {
            background: #2c3e50;
            color: white;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .text-right {
            text-align: right;
        }

        .summary-wrapper {
            display: table;
            width: 100%;
            margin-top: 50px;
        }

        .summary-left,
        .summary-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .summary-right {
            text-align: right;
        }

        .summary {
            width: 100%;
        }

        .summary-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }

        .summary-label,
        .summary-value {
            display: table-cell;
        }

        .summary-label {
            text-align: left;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
        }

        .pe-3 {
            padding-right: 10px
        }

        .total-row {
            background: #2c3e50;
            color: white;
            padding: 12px;
            margin-top: 10px;
            border-radius: 4px;
        }

        .notes {
            clear: both;
            margin-top: 50px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #2c3e50;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
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
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-flex">
                <div class="header-left">
                    @if ($invoice->user->shop_logo)
                        <img src="{{ public_path('storage/' . $invoice->user->shop_logo) }}" alt="Logo"
                            style="max-height: 60px; margin-bottom: 1rem;">
                    @endif
                    <div class="company-name">{{ $invoice->user->shop_name }}</div>
                    @if ($invoice->user->shop_address)
                        <div>{{ $invoice->user->shop_address }}</div>
                    @endif
                    <div>{{ $invoice->user->shop_phone }}</div>
                    <div>{{ $invoice->user->email }}</div>
                </div>
                <div class="header-right">
                    <div class="invoice-title">INVOICE</div>
                    <div style="margin-top: 10px;">
                        @if ($invoice->status === 'paid')
                            <span class="status-badge status-paid">LUNAS</span>
                        @elseif($invoice->status === 'unpaid')
                            <span class="status-badge status-unpaid">BELUM DIBAYAR</span>
                        @else
                            <span class="status-badge status-cancelled">DIBATALKAN</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="info-box">
            <div class="info-flex">
                <div class="info-left">
                    <div><strong>No Invoice:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Tanggal:</strong> {{ $invoice->invoice_date->format('d M Y') }}</div>
                    <div><strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
                </div>
                <div class="info-right">
                    <div style="text-align: right;">
                        <div style="font-size: 11px; color: #6c757d;">Total</div>
                        <div style="font-size: 20px; font-weight: bold; color: #2c3e50;">
                            {{ formatRupiah($invoice->total) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div style="margin-bottom: 20px;">
            <div class="info-label">Kepada:</div>
            <div style="font-weight: bold; font-size: 14px;">{{ $invoice->customer->name }}</div>
            <div>{{ $invoice->customer->phone }}</div>
            @if ($invoice->customer->email)
                <div>{{ $invoice->customer->email }}</div>
            @endif
            @if ($invoice->customer->address)
                <div>{{ $invoice->customer->address }}</div>
            @endif
        </div>

        <!-- Items -->
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right" style="width: 80px;">Qty</th>
                    <th class="text-right" style="width: 120px;">Harga</th>
                    <th class="text-right" style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if ($item->description)
                                <br><span style="color: #6c757d; font-size: 11px;">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ formatRupiah($item->price) }}</td>
                        <td class="text-right"><strong>{{ formatRupiah($item->total) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-wrapper">
            <div class="summary-left"></div>
            <div class="summary-right">
                <div class="summary-row">
                    <br>
                    <div class="summary-label">Subtotal:</div>
                    <div class="summary-value pe-3">{{ formatRupiah($invoice->subtotal) }}</div>
                </div>
                @if ($invoice->tax_percentage > 0)
                    <div class="summary-row">
                        <div class="summary-label">Pajak ({{ $invoice->tax_percentage }}%):</div>
                        <div class="summary-value pe-3">{{ formatRupiah($invoice->tax_amount) }}</div>
                    </div>
                @endif
                @if ($invoice->discount_amount > 0)
                    <div class="summary-row">
                        <div class="summary-label">Diskon:</div>
                        <div class="summary-value pe-3" style="color: #ef4444;">
                            -{{ formatRupiah($invoice->discount_amount) }}
                        </div>
                    </div>
                @endif
                <div class="total-row">
                    <div class="summary-row">
                        <div class="summary-label" style="font-size: 16px;">TOTAL:</div>
                        <div class="summary-value" style="font-size: 16px;">{{ formatRupiah($invoice->total) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Notes -->
        @if ($invoice->notes)
            <div class="notes">
                <strong>Catatan:</strong><br>
                {{ $invoice->notes }}
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda!</p>
            <p>Invoice ini dibuat oleh {{ $invoice->user->shop_name }}</p>
        </div>
    </div>
</body>

</html>
