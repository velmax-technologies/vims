<?php

namespace Modules\Supplier\Http\Requests;

use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierRequest extends FormRequest
{
    use ApiResponseFormatTrait;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $requetMethod = $this->getMethod();
        $rules = [];

        if ($requetMethod == 'POST') {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'contact_person' => 'nullable|string|max:255',
            ];
        } elseif ($requetMethod == 'PUT' || $requetMethod == 'PATCH') {
            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|nullable|email|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'address' => 'sometimes|nullable|string|max:500',
                'contact_person' => 'sometimes|nullable|string|max:255',
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
