<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'order_id' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'min:3', 'max:15'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'order_id.required' => 'Order ID is required.',
            'phone.required' => 'Phone number is required.',
            'rating.required' => 'Rating is required.',
            'review.required' => 'Review text is required.',
        ];
    }
}
