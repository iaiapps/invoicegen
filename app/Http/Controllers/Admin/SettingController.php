<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PlatformSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $admin = Auth::user();

        // Platform stats
        $stats = [
            'total_users' => User::where('id', '!=', $admin->id)->count(),
            'active_users' => User::where('id', '!=', $admin->id)->where('is_active', true)->count(),
            'total_revenue' => \App\Models\Subscription::where('payment_status', 'paid')->sum('amount'),
        ];

        // Get platform settings
        $settings = [
            'free_invoice_limit' => PlatformSetting::get('free_invoice_limit', 30),
            'free_product_limit' => PlatformSetting::get('free_product_limit', 50),
            'basic_price' => PlatformSetting::get('basic_price', 50000),
            'basic_invoice_limit' => PlatformSetting::get('basic_invoice_limit', 60),
            'basic_product_limit' => PlatformSetting::get('basic_product_limit', 200),
            'pro_price' => PlatformSetting::get('pro_price', 100000),
            'pro_invoice_limit' => PlatformSetting::get('pro_invoice_limit', 120),
            'pro_product_limit' => PlatformSetting::get('pro_product_limit', 9999),
            'bank_accounts' => PlatformSetting::get('bank_accounts', []),
            'whatsapp_admin' => PlatformSetting::get('whatsapp_admin', '6281234567890'),
            'platform_name' => PlatformSetting::get('platform_name', 'InvoiceGen'),
            'grace_period_days' => PlatformSetting::get('grace_period_days', 7),
        ];

        return view('admin.settings.index', compact('admin', 'stats', 'settings'));
    }

    public function update(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Update platform configuration
     */
    public function updatePlatformConfig(Request $request)
    {
        $validated = $request->validate([
            'free_invoice_limit' => 'required|integer|min:1|max:999',
            'free_product_limit' => 'required|integer|min:1|max:9999',
            'basic_price' => 'required|integer|min:0',
            'basic_invoice_limit' => 'required|integer|min:1|max:999',
            'basic_product_limit' => 'required|integer|min:1|max:9999',
            'pro_price' => 'required|integer|min:0',
            'pro_invoice_limit' => 'required|integer|min:1|max:999',
            'pro_product_limit' => 'required|integer|min:1|max:9999',
            'whatsapp_admin' => 'required|string|max:20',
            'grace_period_days' => 'required|integer|min:1|max:30',
        ]);

        try {
            // Update each setting
            PlatformSetting::set('free_invoice_limit', $validated['free_invoice_limit'], 'integer');
            PlatformSetting::set('free_product_limit', $validated['free_product_limit'], 'integer');
            PlatformSetting::set('basic_price', $validated['basic_price'], 'integer');
            PlatformSetting::set('basic_invoice_limit', $validated['basic_invoice_limit'], 'integer');
            PlatformSetting::set('basic_product_limit', $validated['basic_product_limit'], 'integer');
            PlatformSetting::set('pro_price', $validated['pro_price'], 'integer');
            PlatformSetting::set('pro_invoice_limit', $validated['pro_invoice_limit'], 'integer');
            PlatformSetting::set('pro_product_limit', $validated['pro_product_limit'], 'integer');
            PlatformSetting::set('whatsapp_admin', $validated['whatsapp_admin'], 'string');
            PlatformSetting::set('grace_period_days', $validated['grace_period_days'], 'integer');

            // Clear all settings cache
            Cache::flush();

            return redirect()->route('admin.settings')
                ->with('success', 'Konfigurasi platform berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
