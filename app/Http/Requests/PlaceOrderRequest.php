<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // ← must be this, not false
    }

    public function rules(): array
    {
        return [
            'shipping.first_name'     => ['required', 'string', 'max:255'],
            'shipping.last_name'      => ['required', 'string', 'max:255'],
            'shipping.address_line_1' => ['required', 'string', 'max:255'],
            'shipping.address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping.city'           => ['required', 'string', 'max:255'],
            'shipping.state'          => ['nullable', 'string', 'max:100'],
            'shipping.postal_code'    => ['required', 'string', 'max:20'],
            'shipping.country'        => ['required', 'string', 'size:2'],
            'coupon_code'             => ['nullable', 'string'],
            'payment_method'          => ['required', 'in:stripe,cod,paypal'],
        ];
    }
}