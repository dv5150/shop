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
        'coupon' => \DV5150\Shop\Models\Deals\Coupon::class,
        'discount' => \DV5150\Shop\Models\Deals\Discount::class,
        'payment' => \DV5150\Shop\Models\Default\Payment::class,
    ],
    'resources' => [
        'shippingMode' => \DV5150\Shop\Http\Resources\ShippingModeResource::class,
        'paymentMode' => \DV5150\Shop\Http\Resources\PaymentModeResource::class,
        'product' => \DV5150\Shop\Http\Resources\ProductResource::class,
        'shippingAddress' => \DV5150\Shop\Http\Resources\ShippingAddressResource::class,
        'category' => \DV5150\Shop\Http\Resources\CategoryResource::class,
    ],
    'support' => [
        'cartItemCapsule' => \DV5150\Shop\Support\ShopItemCapsule::class,
    ],
    'currency' => [
        'code' => 'HUF',
    ],
];
