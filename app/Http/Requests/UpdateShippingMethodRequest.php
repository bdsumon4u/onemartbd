<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'type' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}
