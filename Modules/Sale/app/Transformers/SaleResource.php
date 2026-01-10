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
            'shift_id' => $this->shift_id,
            'cashier' => $this->shift->user->name ?? null,
            'shift_start_time' => $this->shift->start_time?->format('Y-m-d H:i'),
            'shift_end_time' => $this->shift->end_time?->format('Y-m-d H:i'),
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'user' => $this->user->name ?? null,
            'customer' => $this->customer->name ?? null,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'sold_at' => $this->sold_at?->format('Y-m-d H:i'),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
            'sale_items' => $this->sale_items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item' => $item->item->name ?? null,
                    'cost' => $item->cost,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ];
            }), 
        ];
    }
}
