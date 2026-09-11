<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'order_item_id', 'user_id', 'rating_overall', 'rating_rasa', 
        'rating_kualitas', 'rating_packaging', 'rating_pelayanan', 
        'comment', 'photo', 'is_anonymous', 'is_hidden', 
        'admin_reply', 'replied_by', 'replied_at'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_hidden' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}