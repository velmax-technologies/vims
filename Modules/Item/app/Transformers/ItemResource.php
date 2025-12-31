<?php

namespace Modules\Item\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            //'qty' => $this->stocks->sum('quantity') + $this->addition_stock_adjustments->sum('quantity') + ($this->item_return->quantity ?? 0) - $this->subtraction_stock_adjustments->sum('quantity') - ($this->item_sale->quantity ?? 0) ?? 0,
            'quantity' => $this->available_quantity, // Use the accessor for available quantity
            'cost' => $this->costs()->latest()->first()->cost ?? 0,
            'wholesale_price' => $this->item_prices()->withAnyTags(['wholesale'], 'priceTag')->latest()->first()->price ?? "0",
            'retail_price' => $this->item_prices()->withAnyTags(['retail'], 'priceTag')->latest()->first()->price ?? "0",
            'tags' => $this->tags->pluck('name'),
        ];
    }
}
