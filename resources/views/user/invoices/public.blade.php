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
            --radius: 0.5rem;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            body {
                padding: 2rem 1rem;
            }
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .invoice-container {
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            }
        }

        .invoice-body {
            padding: 1.5rem;
        }

        @media (min-width: 768px) {
            .invoice-body {
                padding: 2rem;
            }
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

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: hsl(var(--primary));
            color: white;
            border-color: hsl(var(--primary));
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-outline-secondary {
            background: white;
            color: hsl(var(--primary));
            border-color: hsl(var(--border));
        }

        .btn-outline-secondary:hover {
            background: hsl(var(--muted));
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead tr {
            border-bottom: 2px solid hsl(var(--border));
        }

        .table th {
            padding: 0.75rem 0.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid hsl(var(--border));
        }

        .text-end {
            text-align: right;
        }

        .text-danger {
            color: #ef4444;
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
        <!-- Status & Actions -->
        <div class="invoice-body no-print" style="padding-bottom: 0; border-bottom: 1px solid hsl(var(--border));">
            <div style="text-align: center; margin-bottom: 1.5rem;">
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

                <div style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('invoice.pdf', $invoice->unique_id) }}" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    {{-- <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i> Print
                    </button> --}}
                </div>
            </div>
        </div>

        <!-- Invoice Content -->
        <div class="invoice-body">
            @include('user.invoices._invoice-content', ['invoice' => $invoice])
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
