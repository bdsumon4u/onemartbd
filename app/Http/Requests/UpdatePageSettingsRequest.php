<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'about_us' => ['nullable', 'string'],
            'delivery_policy' => ['nullable', 'string'],
            'return_policy' => ['nullable', 'string'],
        ];
    }
}
