<?php

namespace DV5150\Shop\Tests\Concerns\Services;

use DV5150\Shop\Facades\Shop;
use DV5150\Shop\Tests\Mock\PaymentProviders\TestPaymentProvider;

it('can register a payment provider', function () {
    expect(Shop::getAllPaymentProviders())->toBe([]);

    Shop::registerPaymentProviders([TestPaymentProvider::class]);

    expect(Shop::getAllPaymentProviders())
        ->toBe(['test' => TestPaymentProvider::class])
        ->and(Shop::getPaymentProvider('test'))
        ->toBe(TestPaymentProvider::class);

});