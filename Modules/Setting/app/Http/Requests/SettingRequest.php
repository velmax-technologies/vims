<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Http\Response;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingRequest extends FormRequest
{
       use ApiResponseFormatTrait;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $requetMethod = $this->getMethod();
        $rules = [];

      
        if ($requetMethod === 'PUT' || $requetMethod === 'PATCH') {
            // Define validation rules for settings update
            $rules['settings'] = 'array|required';
            $rules['settings.*.key'] = 'required|string';
            $rules['settings.*.value'] = 'required';
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
