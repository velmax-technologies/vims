<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'quantity',
    ];

    // menu item belongs to item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
