<?php

namespace Modules\Order\Http\Requests;

use App\Models\Item;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    use ApiResponseFormatTrait;
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         $rules = [];

        //$availableQuantity = 0;

        if ($this->isMethod('post')) {
            $rules = [
                'customer_id' => 'required|exists:customers,id',
                'user_id' => 'required|exists:users,id',
                'total_amount' => 'required|numeric|min:0',
                'note' => 'nullable|string|max:255',
                'order_items' => 'array',
                'order_items.*.item_id' => 'required|exists:items,id',
                'order_items.*.price' => 'required|numeric|min:0',
                'order_items.*.quantity' => [
                    'required',
                    'numeric',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $itemId = $this->order_items[$index]['item_id'] ?? null;
                        if ($itemId) {
                            $item = Item::find($itemId);
                            $available = $item->available_quantity;
                            if ($value > $available && !$item->is_kitchen_menu) {
                                $fail("Only ($available) pieces of $item->name are available in stock.");
                            }
                        }
                    }
                ],

            ];
        }

         if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'status' => 'required|in:pending,completed,cancelled',
                'customer_id' => 'sometimes|required|exists:customers,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'total_amount' => 'sometimes|required|numeric|min:0',
                'placed_at' => 'sometimes|required|date',
                'note' => 'nullable|string|max:255',
            ];
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
