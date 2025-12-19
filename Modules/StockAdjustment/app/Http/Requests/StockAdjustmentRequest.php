<?php

namespace Modules\StockAdjustment\Http\Requests;

use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockAdjustmentRequest extends FormRequest
{
    use ApiResponseFormatTrait;
    /**
     * 
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('POST')) {
            $rules = [
                //'adjustment_date' => 'required|date',
                'reason' => 'required|string|max:255',
                'item_id' => 'required|exists:items,id',
                'quantity' => 'required|numeric|min:0',
                'type' => 'required|in:addition,subtraction',
                'note' => 'nullable|string|max:500',
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
