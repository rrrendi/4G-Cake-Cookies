<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_phone', 
        'customer_email', 'pickup_delivery_date', 'pickup_delivery_slot', 
        'delivery_method', 'delivery_address', 'delivery_city', 
        'delivery_district', 'delivery_postal_code', 'subtotal', 
        'shipping_cost', 'packaging_cost', 'total', 'status', 'notes'
    ];

    protected $casts = [
        'pickup_delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}