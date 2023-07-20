<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\ShippingModeContract;

test('shipping mode can be converted to order item', function () {
    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()->create();

    $expected = new (config('shop.models.orderItem'))([
        'name' => $shippingMode->getName(),
        'quantity' => 1,
        'price_gross' => $shippingMode->getPriceGross(),
        'info' => null,
    ]);

    expect($shippingMode->toOrderItem()->is($expected))->toBeTrue();
});