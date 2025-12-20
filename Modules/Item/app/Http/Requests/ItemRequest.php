<?php

namespace Modules\Item\Http\Requests;

use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemRequest extends FormRequest
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
                'item' => 'required|string|max:255|unique:items,name',
                'alias' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:1000',
                'quantity' => 'nullable|integer|min:0',
                'note' => 'nullable|string|max:500',
                'expiry_date' => 'nullable|date',
                'is_expired' => 'nullable|boolean',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50', // Each tag should be a string with a max length
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $itemId = $this->route('id'); // Assuming 'id' is the route parameter name
            $rules = [
                'name' => 'sometimes|required|string|max:255|unique:items,name,' . $itemId,
                'alias' => 'sometimes|nullable|string|max:100',
                'description' => 'sometimes|nullable|string|max:1000',
                'note' => 'sometimes|nullable|string|max:500',
                'tags' => 'sometimes|nullable|array',
                'tags.*' => 'string|max:50', // Each tag should be a string with a max length
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
