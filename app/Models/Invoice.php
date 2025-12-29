<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'unique_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'discount_amount',
        'total',
        'notes',
        'status',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'integer',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'integer',
        'discount_amount' => 'integer',
        'total' => 'integer',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->unique_id)) {
                $invoice->unique_id = Str::random(12);
            }

            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber($invoice->user_id);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getPublicUrl()
    {
        return route('invoice.public', $this->unique_id);
    }

    public function getWhatsAppUrl()
    {
        $message = urlencode(
            "Halo {$this->customer->name},\n\n" .
                "Berikut invoice dari {$this->user->shop_name}:\n" .
                $this->getPublicUrl() . "\n\n" .
                "No Invoice: {$this->invoice_number}\n" .
                "Total: Rp " . number_format($this->total, 0, ',', '.') . "\n" .
                "Jatuh Tempo: " . $this->due_date->format('d/m/Y') . "\n\n" .
                "Terima kasih!"
        );

        return "https://wa.me/{$this->customer->formatted_phone}?text={$message}";
    }

    public static function generateInvoiceNumber($userId)
    {
        $date = now()->format('Ym');
        $lastInvoice = static::where('user_id', $userId)
            ->whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])
            ->latest('id')
            ->first();

        $number = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return 'INV-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
