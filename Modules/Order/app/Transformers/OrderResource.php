<?php

namespace Modules\Order\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'user' => $this->user->name ?? null,
            'customer' => $this->customer->name ?? null,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'sold_at' => $this->sold_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_items' => $this->order_items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item->name ?? null,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ];
            }), 
        ];
    }
}
