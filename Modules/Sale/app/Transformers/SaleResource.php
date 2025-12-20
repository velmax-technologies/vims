<?php

namespace Modules\Sale\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name ?? null,
            'customer' => $this->customer->name ?? null,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'sold_at' => $this->sold_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sale_items' => $this->sale_items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ];
            }), 
        ];
    }
}
