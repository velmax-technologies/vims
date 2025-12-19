<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'quantity',
    ];

    /**
     * Get the item associated with the return.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the total quantity formatted.
     */
    public function getQuantityAttribute($value)
    {
        return number_format($value, 2, '.', ''); // Format quantity to two decimal places
    }
}
