<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'total_amount',
        'placed_at',
        'note',
        'status',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // customer relationship
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // order items relationship
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
