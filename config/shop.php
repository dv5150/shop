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
    'currency' => [
        'code' => 'HUF',
    ],
];
