<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_logo',
        'subscription_plan',
        'subscription_ends_at',
        'invoice_limit',
        'invoice_count_this_month',
        'product_limit',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    // Helper methods
    public function isSubscriptionActive()
    {
        // Free plan is always active
        if ($this->subscription_plan === 'free') {
            return true;
        }

        // Paid plans (basic & pro) need valid subscription_ends_at
        return in_array($this->subscription_plan, ['basic', 'pro'])
            && $this->subscription_ends_at
            && $this->subscription_ends_at->isFuture();
    }

    public function canCreateInvoice()
    {
        return $this->is_active
            && $this->isSubscriptionActive()
            && $this->invoice_count_this_month < $this->invoice_limit;
    }

    public function getRemainingInvoices()
    {
        return max(0, $this->invoice_limit - $this->invoice_count_this_month);
    }
}
