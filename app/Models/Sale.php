<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'total_amount',
        'sold_at',
        'note',
        'status'
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

    // items relationship
    public function sale_items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
