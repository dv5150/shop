<?php

return [
    'models' => [
        'billingAddress' => \DV5150\Shop\Models\Default\BillingAddress::class,
        'category' => \DV5150\Shop\Models\Default\Category::class,
        'coupon' => \DV5150\Shop\Models\Deals\Coupon::class,
        'discount' => \DV5150\Shop\Models\Deals\Discount::class,
        'order' => \DV5150\Shop\Models\Default\Order::class,
        'orderItem' => \DV5150\Shop\Models\Default\OrderItem::class,
        'payment' => \DV5150\Shop\Models\Default\Payment::class,
        'paymentMode' => \DV5150\Shop\Models\Default\PaymentMode::class,
        'product' => \DV5150\Shop\Models\Default\Product::class,
        'shippingMode' => \DV5150\Shop\Models\Default\ShippingMode::class,
        'shippingAddress' => \DV5150\Shop\Models\Default\ShippingAddress::class,
        'user' => \App\Models\User::class,
    ],
    'resources' => [
        'shippingMode' => \DV5150\Shop\Http\Resources\ShippingModeResource::class,
        'paymentMode' => \DV5150\Shop\Http\Resources\PaymentModeResource::class,
        'product' => \DV5150\Shop\Http\Resources\ProductResource::class,
        'shippingAddress' => \DV5150\Shop\Http\Resources\ShippingAddressResource::class,
        'category' => \DV5150\Shop\Http\Resources\CategoryResource::class,
    ],
    'support' => [
        'shopItemCapsule' => \DV5150\Shop\Support\ShopItemCapsule::class,
    ],
    'currency' => [
        'code' => 'HUF',
    ],
];
