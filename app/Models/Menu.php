<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
    ];

    // item
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // menu items
    public function menu_items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
