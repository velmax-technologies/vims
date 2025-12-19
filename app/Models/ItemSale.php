<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'quantity',
    ];

    /**
     * Get the item associated with the sale.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the sale associated with the item sale.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

 
    /**
     * Get the total price formatted.
     */
    public function getTotalAttribute($value)
    {
        return number_format($value, 2, '.', ''); // Format total to two decimal places
    }
}
