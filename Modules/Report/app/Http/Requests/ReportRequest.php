<?php

namespace Modules\Report\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('post')) {
            $rules = [
                'title' => 'required|string|unique:reports,title',
                'description' => 'nullable|string',
                'report_date' => 'nullable|date',
                // Additional rules can be added here
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
