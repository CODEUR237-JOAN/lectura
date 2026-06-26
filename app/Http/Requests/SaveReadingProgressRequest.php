<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveReadingProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_page'     => ['nullable', 'integer', 'min:0'],
            'current_location' => ['nullable', 'string', 'max:2048'],
            'total_pages'      => ['nullable', 'integer', 'min:0'],
            'progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_finished'      => ['nullable', 'boolean'],
        ];
    }
}
