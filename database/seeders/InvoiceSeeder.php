<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Invoice 1 - Paid
        $invoice1 = Invoice::create([
            'user_id' => 2, // Trial User
            'customer_id' => 1, // PT. Maju Jaya
            'invoice_number' => 'INV-' . now()->format('Ym') . '-0001',
            'unique_id' => Str::random(16),
            'issue_date' => Carbon::now()->subDays(10),
            'due_date' => Carbon::now()->subDays(3),
            'subtotal' => 5150000,
            'tax' => 0,
            'discount' => 150000,
            'total' => 5000000,
            'notes' => 'Terima kasih atas pesanan Anda',
            'status' => 'paid',
        ]);

        // Invoice items untuk invoice 1
        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'product_id' => 1, // Laptop HP Core i5
            'description' => 'Laptop HP Core i5, RAM 8GB, SSD 256GB',
            'quantity' => 1,
            'price' => 5000000,
            'total' => 5000000,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'product_id' => 2, // Mouse Wireless Logitech
            'description' => 'Mouse Wireless Logitech M185',
            'quantity' => 1,
            'price' => 150000,
            'total' => 150000,
        ]);

        // Invoice 2 - Unpaid
        $invoice2 = Invoice::create([
            'user_id' => 2, // Trial User
            'customer_id' => 2, // CV. Berkah Abadi
            'invoice_number' => 'INV-' . now()->format('Ym') . '-0002',
            'unique_id' => Str::random(16),
            'issue_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(2),
            'subtotal' => 1700000,
            'tax' => 0,
            'discount' => 0,
            'total' => 1700000,
            'notes' => 'Mohon transfer ke rekening yang tertera',
            'status' => 'unpaid',
        ]);

        // Invoice items untuk invoice 2
        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'product_id' => 3, // Keyboard Mechanical
            'description' => 'Keyboard Mechanical RGB',
            'quantity' => 2,
            'price' => 850000,
            'total' => 1700000,
        ]);

        // Invoice 3 - Unpaid & Overdue (untuk Basic User)
        $invoice3 = Invoice::create([
            'user_id' => 3, // Basic User
            'customer_id' => 1,
            'invoice_number' => 'INV-' . now()->format('Ym') . '-0003',
            'unique_id' => Str::random(16),
            'issue_date' => Carbon::now()->subDays(15),
            'due_date' => Carbon::now()->subDays(5), // Sudah lewat jatuh tempo
            'subtotal' => 5000000,
            'tax' => 500000,
            'discount' => 0,
            'total' => 5500000,
            'notes' => 'Pembayaran sudah jatuh tempo',
            'status' => 'unpaid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice3->id,
            'product_id' => 1,
            'description' => 'Laptop HP Core i5, RAM 8GB, SSD 256GB',
            'quantity' => 1,
            'price' => 5000000,
            'total' => 5000000,
        ]);

        // Invoice 4 - Paid (untuk Basic User)
        $invoice4 = Invoice::create([
            'user_id' => 3, // Basic User
            'customer_id' => 2,
            'invoice_number' => 'INV-' . now()->format('Ym') . '-0004',
            'unique_id' => Str::random(16),
            'issue_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(13),
            'subtotal' => 3000000,
            'tax' => 300000,
            'discount' => 0,
            'total' => 3300000,
            'notes' => 'Lunas, terima kasih',
            'status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice4->id,
            'product_id' => 2,
            'description' => 'Mouse Wireless Logitech M185',
            'quantity' => 20,
            'price' => 150000,
            'total' => 3000000,
        ]);

        // Invoice 5 - Cancelled
        $invoice5 = Invoice::create([
            'user_id' => 3, // Basic User
            'customer_id' => 1,
            'invoice_number' => 'INV-' . now()->format('Ym') . '-0005',
            'unique_id' => Str::random(16),
            'issue_date' => Carbon::now()->subDays(8),
            'due_date' => Carbon::now()->addDays(7),
            'subtotal' => 850000,
            'tax' => 0,
            'discount' => 0,
            'total' => 850000,
            'notes' => 'Dibatalkan oleh customer',
            'status' => 'cancelled',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice5->id,
            'product_id' => 3,
            'description' => 'Keyboard Mechanical RGB',
            'quantity' => 1,
            'price' => 850000,
            'total' => 850000,
        ]);
    }
}
