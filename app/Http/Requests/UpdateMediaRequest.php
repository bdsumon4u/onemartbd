<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:media,id'],
            'file' => ['required', 'file', 'max:4096', 'mimes:jpg,jpeg,png,webp,gif'],
        ];
    }
}
