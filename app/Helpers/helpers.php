<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Rupiah currency
     *
     * @param float $amount
     * @param bool $showPrefix
     * @return string
     */
    function formatRupiah($amount, $showPrefix = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $showPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('formatPhone')) {
    /**
     * Format phone number for WhatsApp
     *
     * @param string $phone
     * @return string
     */
    function formatPhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}

if (!function_exists('canCreateInvoice')) {
    /**
     * Check if user can create invoice
     *
     * During grace period or after downgrade, check against FREE tier limit (30)
     *
     * @return bool
     */
    function canCreateInvoice()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin can always create
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if active
        if (!$user->is_active) {
            return false;
        }

        // Check invoice limit
        $currentLimit = $user->invoice_limit;

        // If subscription expired (grace period or free), check against FREE limit (30)
        if (in_array($user->subscription_plan, ['basic', 'pro'])) {
            if ($user->subscription_ends_at && now()->greaterThan($user->subscription_ends_at)) {
                // In grace period or expired - use FREE tier limit
                $currentLimit = 30;
            }
        }

        // Check invoice count this month against current limit
        if ($user->invoice_count_this_month >= $currentLimit) {
            return false;
        }

        return true;
    }
}

if (!function_exists('canCreateProduct')) {
    /**
     * Check if user can create product based on their subscription plan
     * Uses product_limit from user table (updated when subscription changes)
     *
     * @return bool
     */
    function canCreateProduct()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin can always create
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if active
        if (!$user->is_active) {
            return false;
        }

        // Get current product count
        $currentCount = \App\Models\Product::where('user_id', $user->id)->count();

        // Use product_limit from user table (set during subscription change)
        $limit = $user->product_limit ?? getProductLimit($user->subscription_plan);

        return $currentCount < $limit;
    }
}

if (!function_exists('getProductLimit')) {
    /**
     * Get product limit for a subscription plan from platform settings
     * Used as fallback or for display purposes
     *
     * @param string $plan
     * @return int
     */
    function getProductLimit($plan = 'free')
    {
        return match ($plan) {
            'free' => (int) \App\Models\PlatformSetting::get('free_product_limit', 50),
            'basic' => (int) \App\Models\PlatformSetting::get('basic_product_limit', 200),
            'pro' => (int) \App\Models\PlatformSetting::get('pro_product_limit', 9999),
            default => 50,
        };
    }
}

if (!function_exists('getRemainingProducts')) {
    /**
     * Get remaining product quota
     *
     * @return int
     */
    function getRemainingProducts()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user) {
            return 0;
        }

        if ($user->hasRole('admin')) {
            return 9999;
        }

        $currentCount = \App\Models\Product::where('user_id', $user->id)->count();

        // Use product_limit from user table (set during subscription change)
        $limit = $user->product_limit ?? getProductLimit($user->subscription_plan);

        return max(0, $limit - $currentCount);
    }
}

if (!function_exists('getRemainingInvoices')) {
    /**
     * Get remaining invoice quota
     *
     * @return int
     */
    function getRemainingInvoices()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user) {
            return 0;
        }

        if ($user->hasRole('admin')) {
            return 9999;
        }

        return max(0, $user->invoice_limit - $user->invoice_count_this_month);
    }
}

if (!function_exists('getSubscriptionBadgeClass')) {
    /**
     * Get badge class for subscription plan
     *
     * @param string $plan
     * @return string
     */
    function getSubscriptionBadgeClass($plan)
    {
        return match ($plan) {
            'free' => 'badge-secondary',
            'basic' => 'badge-primary',
            'pro' => 'badge-success',
            default => 'badge-secondary',
        };
    }
}

if (!function_exists('getInvoiceStatusBadge')) {
    /**
     * Get badge HTML for invoice status
     *
     * @param string $status
     * @return string
     */
    function getInvoiceStatusBadge($status)
    {
        return match ($status) {
            'paid' => '<span class="badge badge-success p-1 px-2"><i class="me-2 bi bi-check-circle"></i> Lunas</span>',
            'unpaid' => '<span class="badge badge-warning p-1 px-2"><i class="me-2 bi bi-clock"></i> Belum Bayar</span>',
            'cancelled' => '<span class="badge badge-danger p-1 px-2"><i class="me-2 bi bi-x-circle"></i> Dibatalkan</span>',
            default => '<span class="badge badge-secondary">' . ucfirst($status) . '</span>',
        };
    }
}

if (!function_exists('generateInvoiceNumber')) {
    /**
     * Generate unique invoice number
     *
     * @param int $userId
     * @return string
     */
    function generateInvoiceNumber($userId)
    {
        $date = now()->format('Ym');
        $lastInvoice = \App\Models\Invoice::where('user_id', $userId)
            ->whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])
            ->latest('id')
            ->first();

        $number = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return 'INV-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('active_link')) {
    /**
     * Check if current route matches pattern
     *
     * @param string|array $routes
     * @return string
     */
    function active_link($routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            if (request()->routeIs($route . '*')) {
                return 'active';
            }
        }

        return '';
    }
}

if (!function_exists('getSubscriptionStatusBadge')) {
    /**
     * Get subscription status badge HTML
     *
     * @param string $status
     * @return string
     */
    function getSubscriptionStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Menunggu</span>',
            'success' => '<span class="badge badge-success">Berhasil</span>',
            'failed' => '<span class="badge badge-danger">Gagal</span>',
            'expired' => '<span class="badge badge-secondary">Kadaluarsa</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('getSetting')) {
    /**
     * Get platform setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function getSetting($key, $default = null)
    {
        return \App\Models\PlatformSetting::get($key, $default);
    }
}

if (!function_exists('setSetting')) {
    /**
     * Set platform setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    function setSetting($key, $value)
    {
        return \App\Models\PlatformSetting::set($key, $value);
    }
}
