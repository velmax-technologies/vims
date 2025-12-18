<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         $requetMethod = $this->getMethod();
        $rules = [];

        if ($requetMethod === 'POST') {
            // Define validation rules for user creation
            $rules['name'] = 'required|string|min:6|max:255';
            $rules['email'] = 'email|max:255|unique:users,email';
            $rules['phone'] = 'nullable|string|max:15';
            $rules['address'] = 'string';
        }
        if ($requetMethod === 'PUT' || $requetMethod === 'PATCH') {
            // Define validation rules for user update
            $rules['name'] = 'sometimes|required|string|max:255|unique:users,email,' . $this->route('customer');
            $rules['phone'] = 'nullable|string|max:15';
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
