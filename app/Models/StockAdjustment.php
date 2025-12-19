<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'type',
        'reason',
        'user_id',
        'adjusted_at',
    ];

     protected static function booted(): void
    {
        static::creating(function (StockAdjustment $stockAdjustment) {
            $stockAdjustment->user_id ??= auth()->id();
        });
    }
}
