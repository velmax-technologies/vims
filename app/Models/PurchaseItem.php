<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'cost',
        'line_total', // Total amount for the purchase item
    ];

    /**
     * Get the purchase that owns the item.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the item associated with the purchase item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('purchase_item')
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }
}
