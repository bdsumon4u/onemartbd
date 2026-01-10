<?php

declare(strict_types=1);

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

final class StoreLandingOrderWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'status' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],

            'shipping_total' => ['nullable'],
            'total' => ['nullable'],
            'discount_total' => ['nullable'],

            'payment_method' => ['nullable', 'string'],
            'payment_method_title' => ['nullable', 'string'],
            'customer_ip_address' => ['nullable', 'ip'],
            'customer_note' => ['nullable', 'string'],
            'order_key' => ['nullable', 'string'],
            'number' => ['nullable'],

            'billing' => ['required', 'array'],
            'billing.first_name' => ['required', 'string'],
            'billing.last_name' => ['nullable', 'string'],
            'billing.address_1' => ['required', 'string'],
            'billing.address_2' => ['nullable', 'string'],
            'billing.city' => ['nullable', 'string'],
            'billing.email' => ['nullable', 'email'],
            'billing.phone' => ['required', 'string'],

            'shipping_lines' => ['nullable', 'array'],
            'shipping_lines.*.method_title' => ['nullable', 'string'],
            'shipping_lines.*.method_id' => ['nullable', 'string'],
            'shipping_lines.*.total' => ['nullable'],

            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.product_id' => ['required', 'integer'],
            'line_items.*.quantity' => ['required', 'integer', 'min:1'],
            'line_items.*.price' => ['nullable', 'numeric'],
            'line_items.*.subtotal' => ['nullable'],
            'line_items.*.total' => ['nullable'],
            'line_items.*.name' => ['required', 'string'],
            'line_items.*.sku' => ['nullable', 'string'],
            'line_items.*.meta_data' => ['nullable', 'array'],

            '_links' => ['required', 'array'],
            '_links.self' => ['required', 'array', 'min:1'],
            '_links.self.*.href' => ['required', 'url'],
        ];
    }
}


