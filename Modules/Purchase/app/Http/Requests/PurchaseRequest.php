<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseRequest extends FormRequest
{

    use ApiResponseFormatTrait;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('POST')) {
            $rules = [
                'invoice_number' => [
                    'required',
                    'string',
                    'max:255',
                    // Unique per supplier_id
                    'unique:purchases,invoice_number,NULL,id,supplier_id,' . $this->input('supplier_id'),
                ],
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:purchase_date',
                //'status' => 'required|in:pending,completed',
                'total_amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'due_amount' => 'nullable|numeric|min:0',
                'purchase_items' => 'required|array',
                'purchase_items.*.item_id' => 'required|exists:items,id',
                'purchase_items.*.quantity' => 'required|numeric|min:0',
                'purchase_items.*.cost' => 'required|numeric|min:0',
                'purchase_items.*.line_total' => 'required|numeric|min:0',
            ];
        }
        
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'invoice_number' => [
                    'required',
                    'string',
                    'max:255',
                    // Unique per supplier_id, excluding current purchase
                    'unique:purchases,invoice_number,' . $this->route('purchase') . ',id,supplier_id,' . $this->input('supplier_id'),
                ],
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:purchase_date',
                'status' => 'required|in:pending,completed',
                'total_amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'due_amount' => 'nullable|numeric|min:0',
                'purchase_items' => 'required|array',
                'purchase_items.*.item_id' => 'required|required|exists:items,id',
                'purchase_items.*.quantity' => 'required|required|numeric|min:0',
                'purchase_items.*.cost' => 'required|required|numeric|min:0',
                'purchase_items.*.line_total' => 'required|required|numeric|min:0',
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
