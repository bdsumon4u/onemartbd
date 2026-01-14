<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}
