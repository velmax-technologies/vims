<?php

namespace Modules\Sale\Transformers;

use Illuminate\Http\Request;
use Modules\Item\Transformers\ItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'line_total' => $this->line_total,
            'item' => $this->item->name ?? null,
            'sold_at' => $this->sale->sold_at ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
