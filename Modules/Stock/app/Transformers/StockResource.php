<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'stocks' => $this->stocks->map(function($stock) {
                return [
                    'id' => $stock->id,
                    'quantity' => $stock->quantity,
                    'available_quantity' => $stock->available_quantity,
                    'note' => $stock->note,
                    'created_at' => $stock->created_at,
                ];
            }),
        ];
    }
}
