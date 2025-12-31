<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private function getUserId()
    {
        return Auth::id();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::where('user_id', $this->getUserId());

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $products = $query->orderBy($sortBy, $sortOrder)->paginate(12);

        return view('user.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user can create more products
        if (!canCreateProduct()) {
            $limit = getProductLimit(Auth::user()->subscription_plan);
            return redirect()->route('products.index')
                ->with('error', "Anda telah mencapai limit produk ({$limit} produk) untuk paket " . strtoupper(Auth::user()->subscription_plan) . ". Upgrade paket Anda untuk menambah produk.");
        }

        return view('user.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user can create more products
        if (!canCreateProduct()) {
            $limit = getProductLimit(Auth::user()->subscription_plan);
            return redirect()->back()
                ->with('error', "Anda telah mencapai limit produk ({$limit} produk) untuk paket " . strtoupper(Auth::user()->subscription_plan) . ". Upgrade paket Anda untuk menambah produk.")
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,NULL,id,user_id,' . $this->getUserId(),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama produk wajib diisi',
            'sku.unique' => 'SKU sudah digunakan untuk produk lain',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Product::create([
                'user_id' => $this->getUserId(),
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => true,
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Authorization check
        if ($product->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        // Get product usage in invoices
        $invoiceItems = $product->invoiceItems()
            ->with('invoice.customer')
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'total_sold' => $product->invoiceItems()->sum('quantity'),
            'total_revenue' => $product->invoiceItems()->sum('total'),
            'times_used' => $product->invoiceItems()->count(),
        ];

        return view('user.products.show', compact('product', 'invoiceItems', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Authorization check
        if ($product->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Authorization check
        if ($product->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id . ',id,user_id,' . $this->getUserId(),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:on,1,true', // Checkbox can be 'on', '1', 'true', or absent
        ], [
            'name.required' => 'Nama produk wajib diisi',
            'sku.unique' => 'SKU sudah digunakan untuk produk lain',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];
            
            // Log update data for debugging
            \Log::info('Updating product', [
                'product_id' => $product->id,
                'update_data' => $updateData,
                'before' => $product->toArray()
            ]);
            
            $updated = $product->update($updateData);
            
            if (!$updated) {
                throw new \Exception('Failed to update product in database');
            }
            
            // Refresh model to ensure we have latest data
            $product->refresh();
            
            \Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'after' => $product->toArray()
            ]);

            return redirect()->route('products.show', $product)
                ->with('success', 'Produk berhasil diupdate!');
        } catch (\Exception $e) {
            \Log::error('Product update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Authorization check
        if ($product->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if product is used in any invoice items
            $hasInvoiceItems = $product->invoiceItems()->exists();

            if ($hasInvoiceItems) {
                return redirect()->back()
                    ->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam invoice. Anda bisa menonaktifkan produk ini.');
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus(Product $product)
    {
        // Authorization check
        if ($product->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $product->update([
                'is_active' => !$product->is_active
            ]);

            $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->back()
                ->with('success', "Produk berhasil {$status}!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
