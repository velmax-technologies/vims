<?php

namespace Modules\Shift\Transformers;

use Illuminate\Http\Request;
use Modules\Sale\Transformers\SaleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_active' => $this->is_active,
            'sales' => SaleResource::collection($this->sales),
        ];
    }
}
