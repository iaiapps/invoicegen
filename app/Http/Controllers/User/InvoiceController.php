<?php

namespace App\Http\Controllers\User;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    private function getUserid()
    {
        return Auth::id();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer')->where('user_id', $this->getUserid());

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $invoices = $query->orderBy($sortBy, $sortOrder)->paginate(15);

        return view('user.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('user_id', $this->getUserid())
            ->orderBy('name')
            ->get();

        $products = Product::where('user_id', $this->getUserid())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('user.invoices.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'customer_id.required' => 'Customer wajib dipilih',
            'invoice_date.required' => 'Tanggal invoice wajib diisi',
            'due_date.after_or_equal' => 'Jatuh tempo harus sama atau setelah tanggal invoice',
            'items.required' => 'Minimal harus ada 1 item',
            'items.min' => 'Minimal harus ada 1 item',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if customer belongs to user
        $customer = Customer::where('id', $request->customer_id)
            ->where('user_id', $this->getUserid())
            ->first();

        if (!$customer) {
            return redirect()->back()
                ->with('error', 'Customer tidak ditemukan')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $taxPercentage = $request->tax_percentage ?? 0;
            $taxAmount = ($subtotal * $taxPercentage) / 100;
            $discountAmount = $request->discount_amount ?? 0;
            $total = $subtotal + $taxAmount - $discountAmount;

            // Create invoice (invoice_number & unique_id auto-generated in model)
            $invoice = Invoice::create([
                'user_id' => $this->getUserid(),
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'notes' => $request->notes,
                'status' => 'unpaid',
            ]);

            // Create invoice items and auto-save new products
            foreach ($request->items as $item) {
                $productId = $item['product_id'] ?? null;

                // If no product_id (manual input), check if we should save it as new product
                if (!$productId && !empty($item['product_name'])) {
                    // Check if product with same name already exists for this user
                    $existingProduct = Product::where('user_id', $this->getUserid())
                        ->where('name', $item['product_name'])
                        ->first();

                    if ($existingProduct) {
                        // Use existing product
                        $productId = $existingProduct->id;
                    } else {
                        // Check if user can create more products
                        if (canCreateProduct()) {
                            // Create new product automatically
                            $newProduct = Product::create([
                                'user_id' => $this->getUserid(),
                                'name' => $item['product_name'],
                                'description' => $item['description'] ?? null,
                                'price' => $item['price'],
                                'stock' => 0, // Set to 0 for service/non-stock items
                                'is_active' => true,
                            ]);
                            $productId = $newProduct->id;
                        }
                        // If limit reached, still create invoice item without linking to product
                    }
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'product_name' => $item['product_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            // Increment invoice count
            auth()->user()->increment('invoice_count_this_month');

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load(['customer', 'items']);

        return view('user.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot edit paid or cancelled invoices
        if (in_array($invoice->status, ['paid', 'cancelled'])) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Invoice yang sudah dibayar atau dibatalkan tidak dapat diedit');
        }

        $invoice->load('items');

        $customers = Customer::where('user_id', $this->getUserid())
            ->orderBy('name')
            ->get();

        $products = Product::where('user_id', $this->getUserid())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('user.invoices.edit', compact('invoice', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot edit paid or cancelled invoices
        if (in_array($invoice->status, ['paid', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'Invoice yang sudah dibayar atau dibatalkan tidak dapat diedit');
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $taxPercentage = $request->tax_percentage ?? 0;
            $taxAmount = ($subtotal * $taxPercentage) / 100;
            $discountAmount = $request->discount_amount ?? 0;
            $total = $subtotal + $taxAmount - $discountAmount;

            // Update invoice
            $invoice->update([
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'notes' => $request->notes,
            ]);

            // Delete old items
            $invoice->items()->delete();

            // Create new items and auto-save new products
            foreach ($request->items as $item) {
                $productId = $item['product_id'] ?? null;

                // If no product_id (manual input), check if we should save it as new product
                if (!$productId && !empty($item['product_name'])) {
                    // Check if product with same name already exists for this user
                    $existingProduct = Product::where('user_id', $this->getUserid())
                        ->where('name', $item['product_name'])
                        ->first();

                    if ($existingProduct) {
                        // Use existing product
                        $productId = $existingProduct->id;
                    } else {
                        // Check if user can create more products
                        if (canCreateProduct()) {
                            // Create new product automatically
                            $newProduct = Product::create([
                                'user_id' => $this->getUserid(),
                                'name' => $item['product_name'],
                                'description' => $item['description'] ?? null,
                                'price' => $item['price'],
                                'stock' => 0, // Set to 0 for service/non-stock items
                                'is_active' => true,
                            ]);
                            $productId = $newProduct->id;
                        }
                        // If limit reached, still create invoice item without linking to product
                    }
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'product_name' => $item['product_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot delete paid invoices
        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Invoice yang sudah dibayar tidak dapat dihapus');
        }

        try {
            $invoice->delete();

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Public view (no auth required)
     */
    public function publicView($uniqueId)
    {
        $invoice = Invoice::with(['customer', 'items', 'user'])
            ->where('unique_id', $uniqueId)
            ->firstOrFail();

        return view('user.invoices.public', compact('invoice'));
    }

    /**
     * Download PDF
     */
    public function downloadPdf($uniqueId)
    {
        $invoice = Invoice::with(['customer', 'items', 'user'])
            ->where('unique_id', $uniqueId)
            ->firstOrFail();

        $pdf = Pdf::loadView('user.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        // return view('user.invoices.pdf', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send to WhatsApp
     */
    public function sendWhatsApp(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        // Update sent_at
        if (!$invoice->sent_at) {
            $invoice->update(['sent_at' => now()]);
        }

        // Redirect to WhatsApp
        return redirect($invoice->getWhatsAppUrl());
    }

    /**
     * Mark as paid
     */
    public function markPaid(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Invoice berhasil ditandai sebagai lunas!');
    }

    /**
     * Mark as unpaid
     */
    public function markUnpaid(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->update([
            'status' => 'unpaid',
            'paid_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Invoice berhasil ditandai sebagai belum dibayar!');
    }

    /**
     * Cancel invoice
     */
    public function cancel(Invoice $invoice)
    {
        // Authorization check
        if ($invoice->user_id !== $this->getUserid()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot cancel paid invoices
        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Invoice yang sudah dibayar tidak dapat dibatalkan');
        }

        $invoice->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Invoice berhasil dibatalkan!');
    }
}
