<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefineResumeBulletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:10', 'max:1000'],
            'job_context' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
