<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shift_id',
        'user_id',
        'customer_id',
        'total_amount',
        'sold_at',
        'note',
        'status'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->shift_id = Auth::user()->active_shift->id;
            // You may need additional logic here to ensure uniqueness, 
            // e.g., appending a number if the slug already exists.
        });

         
    }

    // shift relationship
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

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

    // item sales relationship
    public function item_sales(): HasMany
    {
        return $this->hasMany(ItemSale::class); 
    }

   

}
