<?php

return [
    'models' => [
        'user' => \App\Models\User::class,
        'product' => \DV5150\Shop\Models\Default\Product::class,
        'category' => \DV5150\Shop\Models\Default\Category::class,
        'order' => \DV5150\Shop\Models\Default\Order::class,
        'orderItem' => \DV5150\Shop\Models\Default\OrderItem::class,
        'paymentMode' => \DV5150\Shop\Models\Default\PaymentMode::class,
        'shippingMode' => \DV5150\Shop\Models\Default\ShippingMode::class,
        'billingAddress' => \DV5150\Shop\Models\Default\BillingAddress::class,
        'shippingAddress' => \DV5150\Shop\Models\Default\ShippingAddress::class,
    ],
    'resources' => [
        'shippingMode' => \DV5150\Shop\Http\Resources\ShippingModeResource::class,
        'paymentMode' => \DV5150\Shop\Http\Resources\PaymentModeResource::class,
        'productList' => \DV5150\Shop\Http\Resources\ProductListResource::class,
    ],
    'currency' => [
        'code' => 'HUF',
    ],
    'defaultShippingMode' => [
        'name' => 'Standard Shipping Service',
        'priceGross' => 990.0,
    ],
    'defaultPaymentMode' => [
        'name' => 'Cash on delivery',
        'priceGross' => 290.0,
    ],
];
