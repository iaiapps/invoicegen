<?php

namespace App\Http\Controllers\User;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Get authenticated user ID
     */
    private function getUserId()
    {
        return Auth::id();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $query = Customer::where('user_id', $this->getUserId());

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $customers = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        return view('user.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^08[0-9]{8,13}$/',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'Nama customer wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx',
            'email.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $customer = Customer::create([
                'user_id' => $this->getUserId(),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'notes' => $request->notes,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'customer' => $customer]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Authorization check
        if ($customer->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        // Get customer invoices
        $invoices = $customer->invoices()
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'total_invoices' => $customer->invoices()->count(),
            'total_revenue' => $customer->invoices()->where('status', 'paid')->sum('total'),
            'unpaid_amount' => $customer->invoices()->where('status', 'unpaid')->sum('total'),
            'unpaid_count' => $customer->invoices()->where('status', 'unpaid')->count(),
        ];

        return view('user.customers.show', compact('customer', 'invoices', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        // Authorization check
        if ($customer->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Authorization check
        if ($customer->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^08[0-9]{8,13}$/',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'Nama customer wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx',
            'email.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'notes' => $request->notes,
            ]);

            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Authorization check
        if ($customer->user_id !== $this->getUserId()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if customer has invoices
            $hasInvoices = $customer->invoices()->exists();

            if ($hasInvoices) {
                return redirect()->back()
                    ->with('error', 'Customer tidak dapat dihapus karena memiliki invoice. Silakan hapus invoice terlebih dahulu.');
            }

            $customer->delete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
