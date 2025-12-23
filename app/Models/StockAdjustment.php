<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'model',
        'model_id',
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

    // item
     public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
