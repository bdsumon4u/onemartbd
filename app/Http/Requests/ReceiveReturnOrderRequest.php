<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveReturnOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'range' => ['nullable', 'string', 'in:today,yesterday,last_3_days,last_month,this_month,last_3_months,last_6_months,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ];
    }
}
