<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\OrderDataTransformerContract;
use Illuminate\Support\Arr;

class OrderDataTransformer implements OrderDataTransformerContract
{
    public function transform($orderData): array
    {
        return [
            'email' => Arr::get($orderData, 'personalData.email'),
            'phone' => Arr::get($orderData, 'personalData.phone'),
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
        ];
    }
}
