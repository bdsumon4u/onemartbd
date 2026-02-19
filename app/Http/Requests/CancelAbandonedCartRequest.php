<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CancelAbandonedCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check() || Auth::guard('manager')->check() || Auth::guard('employee')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Cancellation reason is required',
            'cancellation_reason.min' => 'Cancellation reason must be at least 5 characters',
            'cancellation_reason.max' => 'Cancellation reason must not exceed 500 characters',
        ];
    }
}
