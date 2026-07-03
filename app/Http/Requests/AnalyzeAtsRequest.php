<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeAtsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume' => ['required', 'string', 'min:50', 'max:20000'],
            'job_description' => ['required', 'string', 'min:50', 'max:20000'],
            'cv_id' => ['nullable', 'string', 'exists:cvs,id'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'job_company' => ['nullable', 'string', 'max:255'],
        ];
    }
}
