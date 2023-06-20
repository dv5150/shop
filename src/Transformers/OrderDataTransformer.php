<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\OrderDataTransformerContract;
use Illuminate\Support\Arr;

class OrderDataTransformer implements OrderDataTransformerContract
{
    public function rules(): array
    {
        return [
            'personalData.email' => 'required|email|max:255',
            'personalData.phone' => 'required|string|max:255',
            'personalData.comment' => 'nullable|string',

            'shippingData.name' => 'required|string|max:255',
            'shippingData.zipCode' => 'required|string|max:255',
            'shippingData.city' => 'required|string|max:255',
            'shippingData.street' => 'required|string|max:255',
            'shippingData.comment' => 'nullable|string',

            'billingData.name' => 'required|string|max:255',
            'billingData.zipCode' => 'required|string|max:255',
            'billingData.city' => 'required|string|max:255',
            'billingData.street' => 'required|string|max:255',
            'billingData.taxNumber' => 'nullable|string|max:255',

            'cartData' => 'required|array|min:1',
            'cartData.*.item.id' => 'required|exists:products,id',
            'cartData.*.quantity' => 'required|integer|min:1',

            'shippingMode.provider' => 'required|exists:shipping_modes,provider',
            'paymentMode.provider' => 'required|exists:payment_modes,provider',
        ];
    }

    public function transform(array $orderData): array
    {
        return [
            'email' => Arr::get($orderData, 'personalData.email'),
            'phone' => Arr::get($orderData, 'personalData.phone'),
            'comment' => Arr::get($orderData, 'personalData.comment'),

            'shipping_name' => Arr::get($orderData, 'shippingData.name'),
            'shipping_zip_code' => Arr::get($orderData, 'shippingData.zipCode'),
            'shipping_city' => Arr::get($orderData, 'shippingData.city'),
            'shipping_address' => Arr::get($orderData, 'shippingData.street'),
            'shipping_comment' => Arr::get($orderData, 'shippingData.comment'),

            'billing_name' => Arr::get($orderData, 'billingData.name'),
            'billing_zip_code' => Arr::get($orderData, 'billingData.zipCode'),
            'billing_city' => Arr::get($orderData, 'billingData.city'),
            'billing_address' => Arr::get($orderData, 'billingData.street'),
            'billing_tax_number' => Arr::get($orderData, 'billingData.taxNumber'),

            'shipping_mode_provider' => Arr::get($orderData, 'shippingMode.provider'),
            'payment_mode_provider' => Arr::get($orderData, 'paymentMode.provider'),
        ];
    }
}
