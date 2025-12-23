<?php

namespace Modules\StockAdjustment\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
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
                'reason' => 'required|string|max:255',
                'item_id' => 'required|exists:items,id',
                'quantity' => 'required|numeric|min:0',
                'type' => 'required|in:addition,subtraction,correction',
                'note' => 'nullable|string|max:500',
                //'model' => 'required_if:type,correction|string|max:255',
                //'model_id' => 'required_if:type,correction|integer',
                'model' => [
                    Rule::requiredIf(function () {
                        return $this->input('type') == 'correction' || $this->input('type') == 'subtraction';
                    }),
                ],
                'model_id' => [
                    Rule::requiredIf(function () {
                        return $this->input('type') == 'correction' || $this->input('type') == 'subtraction';
                    }),
                ],
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
