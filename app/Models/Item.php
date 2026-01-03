<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasTags;
    use LogsActivity;
    use SoftDeletes;

    
    protected $fillable = [
        'name',
        'slug',
        'alias',
        'quantity',
        'description',
        'unit_id',
        'sku',
        'upc',
        'note',
        'image_path',
        'is_active',
        'is_kitchen_menu',
    ];

    //casts
    protected $casts = [
        'available_quantity' => 'float',
        'is_active' => 'boolean',
        'is_kitchen_menu' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->slug = Str::slug($item->name);
            // You may need additional logic here to ensure uniqueness, 
            // e.g., appending a number if the slug already exists.
        });

        static::deleting(function ($item) {
            $item->stocks()->delete();
            $item->costs()->delete();
            $item->item_prices()->delete();
        });
    }

    // logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('item')
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    /**
     * Get the item's name.
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    // available quantity
    public function getAvailableQuantityAttribute()
    {
        // Adjust 'quantity' if your Stock model uses a different field name
        /////return $this->stocks()->sum('quantity') + $this->addition_stock_adjustments->sum('quantity') + ($this->item_return->quantity ?? 0) - $this->subtraction_stock_adjustments->sum('quantity') - ($this->item_sale->quantity ?? 0) ?? 0;
        return $this->stocks()->sum('available_quantity') ?? 0;
    }

    // stock
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // stock adjustments
    public function stock_adjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    // addition stock adjustments
    public function addition_stock_adjustments()
    {
        return $this->hasMany(StockAdjustment::class)->where('type', 'addition');
    }

    // subtraction stock adjustments
    public function subtraction_stock_adjustments()
    {
        return $this->hasMany(StockAdjustment::class)->where('type', 'subtraction');
    }

    // costs
    public function costs()
    {
        return $this->hasMany(ItemCost::class);
    }

    // price
    public function item_prices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    // item sales
    public function item_sales(): HasMany
    {
        return $this->hasMany(ItemSale::class);
    }

    // item return
    public function item_return(): HasOne
    {
        return $this->hasOne(ItemReturn::class);
    }

    // menu
    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
    }

    // menu items
    public function menu_items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    
}
