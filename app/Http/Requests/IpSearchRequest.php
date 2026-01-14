<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IpSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $query = $this->input('query');

        if (is_string($query)) {
            $this->merge([
                'query' => trim($query),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'ip'],
        ];
    }
}
