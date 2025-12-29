<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'description',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'total' => 'integer',
    ];

    // Mutator untuk handle null values
    public function setProductIdAttribute($value)
    {
        if ($value === 'null' || $value === '' || $value === 'undefined') {
            $this->attributes['product_id'] = null;
        } else {
            $this->attributes['product_id'] = $value;
        }
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
