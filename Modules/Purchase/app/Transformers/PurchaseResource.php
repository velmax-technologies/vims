<?php

namespace Modules\Purchase\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'supplier_id' => $this->supplier_id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'purchase_items' => PurchaseItemResource::collection($this->whenLoaded('purchase_items')),
            'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
