<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use Illuminate\Database\Eloquent\Collection;

test('payment mode has shipping modes', function () {
    /** @var ShippingModeContract $shippingMode */
    $shippingModes = config('shop.models.shippingMode')::factory()
        ->count(3)
        ->create();

    $shippingModes->each(function (ShippingModeContract $shippingMode) {
        $shippingMode->paymentModes()
            ->sync(config('shop.models.paymentMode')::factory()->count(3)->create());
    });

    $this->assertInstanceOf(Collection::class, $shippingModes);

    $shippingModes->each(function ($shippingMode) {
        $this->assertInstanceOf(ShippingModeContract::class, $shippingMode);
    });
});

test('payment mode can be converted to order item', function () {
    /** @var PaymentModeContract $paymentMode */
    $paymentMode = config('shop.models.paymentMode')::factory()->create();

    $expected = new (config('shop.models.orderItem'))([
        'name' => $paymentMode->getName(),
        'quantity' => 1,
        'price_gross' => $paymentMode->getPriceGross(),
        'info' => null,
    ]);

    expect($paymentMode->toOrderItem()->is($expected))->toBeTrue();
});