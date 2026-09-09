<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInvoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'invoice_date', 'customer_name',
        'customer_email', 'customer_phone', 'street_address', 'postal_code',
        'city', 'klantnummer', 'subtotal', 'tax_percentage', 'tax_amount',
        'discount_amount', 'shipping_cost', 'total', 'payment_method', 'pdf_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
