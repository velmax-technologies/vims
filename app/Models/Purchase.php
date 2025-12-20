<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'purchase_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'invoice_number',
        'notes',
        'is_paid',
        'status',
    ];

    // logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('purchase')
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options

    }

    /**
     * Get the purchase's total amount.
     */
    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 2, '.', ''); // Format total amount to two decimal places
    }

    // purchase items
    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);     
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            $purchase->notes = "Purchase - Invoice #" . $purchase->invoice_number;
            // You may need additional logic here to ensure uniqueness, 
            // e.g., appending a number if the slug already exists.
        });

        

         
    }
}
