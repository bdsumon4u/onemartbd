<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:1,2,3'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:0,1'],
            'password' => ['required', 'string', 'min:6'],
            'start_time' => ['nullable', 'date_format:h:i:s A'],
            'end_time' => ['nullable', 'date_format:h:i:s A'],
        ];
    }
}
