<?php

namespace Modules\Sale\Http\Requests;

use App\Models\Item;
use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaleRequest extends FormRequest
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
                //'sold_at' => 'required|date',
                'note' => 'nullable|string|max:255',
                'sale_items' => 'required|array',
                'sale_items.*.item_id' => 'required|exists:items,id',
                //'sale_items.*.quantity' => 'required|numeric|min:1',
                'sale_items.*.price' => 'required|numeric|min:0',
                'sale_items.*.quantity' => [
                    'required',
                    'numeric',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $itemId = $this->sale_items[$index]['item_id'] ?? null;
                        if ($itemId) {
                            $item = Item::find($itemId);
                            $available = $item->available_quantity;
                            if ($value > $available) {
                                $fail("Only ($available) pieces of $item->name are available in stock.");
                            }
                        }
                    }
                ],

            ];
        }
            
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'customer_id' => 'sometimes|required|exists:customers,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'total_amount' => 'sometimes|required|numeric|min:0',
                'sold_at' => 'sometimes|required|date',
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($this->validationFailedResponse($validator->errors()->first()), Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
