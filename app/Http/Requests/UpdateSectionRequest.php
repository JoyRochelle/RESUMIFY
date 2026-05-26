<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership checked in controller
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'   => ['sometimes', 'string', 'max:100'],
            'content' => ['sometimes', 'nullable'],

            // Personal info fields (when content is a flat object)
            'content.name'     => ['sometimes', 'nullable', 'string', 'max:100'],
            'content.title'    => ['sometimes', 'nullable', 'string', 'max:120'],
            'content.email'    => ['sometimes', 'nullable', 'email', 'max:150'],
            'content.phone'    => ['sometimes', 'nullable', 'string', 'max:30', 'regex:/^[+\d\s\-().]{0,30}$/'],
            'content.location' => ['sometimes', 'nullable', 'string', 'max:200'],
            'content.summary'  => ['sometimes', 'nullable', 'string', 'max:2000'],

            // List-based section items (work_experience, education, skills)
            'content.*.title'       => ['sometimes', 'nullable', 'string', 'max:120'],
            'content.*.company'     => ['sometimes', 'nullable', 'string', 'max:120'],
            'content.*.start_date'  => ['sometimes', 'nullable', 'string', 'max:10'],
            'content.*.end_date'    => ['sometimes', 'nullable', 'string', 'max:10'],
            'content.*.description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'content.*.degree'      => ['sometimes', 'nullable', 'string', 'max:120'],
            'content.*.school'      => ['sometimes', 'nullable', 'string', 'max:120'],
            'content.*.name'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'content.*.level'       => ['sometimes', 'nullable', 'string', 'in:Beginner,Elementary,Intermediate,Advanced,Expert'],
            'content.job_title'       => ['sometimes', 'nullable', 'string', 'max:150'],
            'content.job_description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.email.email'        => 'The email address is not valid.',
            'content.phone.regex'        => 'The phone number contains invalid characters.',
            'content.*.level.in'         => 'Proficiency level must be one of: Beginner, Elementary, Intermediate, Advanced, Expert.',
            'content.*.description.max'  => 'Description must not exceed 2000 characters.',
            'content.summary.max'        => 'Summary must not exceed 2000 characters.',
        ];
    }

    /**
     * Strip HTML tags from all string inputs to prevent XSS.
     */
    protected function prepareForValidation(): void
    {
        $content = $this->input('content');

        if (is_array($content)) {
            array_walk_recursive($content, function (&$value) {
                if (is_string($value)) {
                    $value = strip_tags(trim($value));
                }
            });
            $this->merge(['content' => $content]);
        }
    }
}
