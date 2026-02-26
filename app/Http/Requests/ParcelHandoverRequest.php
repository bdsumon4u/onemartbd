<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParcelHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $invoiceId = $this->input('invoice_id');

        if (is_string($invoiceId)) {
            $this->merge([
                'invoice_id' => trim($invoiceId),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['nullable', 'string', 'max:255', 'exists:orders,invoice_id'],
            'date' => ['nullable', 'date'],
            'range' => ['nullable', 'string', 'in:today,yesterday,last_3_days,last_month,this_month,last_3_months,last_6_months,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ];
    }
}
