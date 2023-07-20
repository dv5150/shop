<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('a shipping mode has its attached payment modes aswell', function () {
    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->shippingMode
        ->toBeNull();

    post(route('api.shop.cart.shippingMode.store', [
        'provider' => $shippingMode->getProvider(),
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->shippingMode
        ->toBe([
            'provider' => $shippingMode->getProvider(),
            'name' => $shippingMode->getName(),
            'priceGross' => $shippingMode->getPriceGross(),
            'componentName' => $shippingMode->getComponentName(),
            'paymentModes' => $shippingMode
                ->paymentModes()
                ->get()
                ->map(fn (PaymentModeContract $paymentMode) => [
                    'provider' => $paymentMode->getProvider(),
                    'name' => $paymentMode->getName(),
                    'priceGross' => $paymentMode->getPriceGross(),
                ])->all()
        ]);
});

test('payment mode not attached to any shipping modes cannot be selected', function () {
    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe([
            'items' => [],
            'coupon' => null,
            'subtotal' => 0.0,
            'total' => 0.0,
            'currency' => [
                'code' => 'HUF',
            ],
            'availableShippingModes' => [],
            'shippingMode' => null,
            'paymentMode' => null,
            'preSavedShippingAddresses' => [],
            'messages' => null,
        ]);

    config('shop.models.shippingMode')::factory()
        ->count(2)
        ->state(new Sequence(
            [
                'name' => 'ABC',
                'price_gross' => 1.0,
                'provider' => 'abc',
            ],
            [
                'name' => 'DEF',
                'price_gross' => 1.0,
                'provider' => 'def',
            ],
        ))->create();

    config('shop.models.shippingMode')::firstWhere('provider', 'abc')
        ->paymentModes()
        ->saveMany([
            new (config('shop.models.paymentMode'))([
                'name' => 'IJK',
                'price_gross' => 1.0,
                'provider' => 'ijk',
            ]),
            new (config('shop.models.paymentMode'))([
                'name' => 'LMN',
                'price_gross' => 1.0,
                'provider' => 'lmn',
            ]),
        ]);

    config('shop.models.shippingMode')::firstWhere('provider', 'def')
        ->paymentModes()
        ->saveMany([
            new (config('shop.models.paymentMode'))([
                'name' => 'OPQ',
                'price_gross' => 1.0,
                'provider' => 'opq',
            ]),
            new (config('shop.models.paymentMode'))([
                'name' => 'RST',
                'price_gross' => 1.0,
                'provider' => 'rst',
            ]),
        ]);

    // shipping mode 1

    post(route('api.shop.cart.shippingMode.store', [
        'provider' => 'abc',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->shippingMode
        ->paymentModes
        ->toBe([
            [
                'provider' => 'ijk',
                'name' => 'IJK',
                'priceGross' => 1.0,
            ],
            [
                'provider' => 'lmn',
                'name' => 'LMN',
                'priceGross' => 1.0,
            ]
        ]);

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'ijk',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBe([
            'provider' => 'ijk',
            'name' => 'IJK',
            'priceGross' => 1.0,
        ]);

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'lmn',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBe([
            'provider' => 'lmn',
            'name' => 'LMN',
            'priceGross' => 1.0,
        ]);

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'opq',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBeNull();

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'rst',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBeNull();

    // shipping mode 2

    post(route('api.shop.cart.shippingMode.store', [
        'provider' => 'def',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->shippingMode
        ->paymentModes
        ->toBe([
            [
                'provider' => 'opq',
                'name' => 'OPQ',
                'priceGross' => 1.0,
            ],
            [
                'provider' => 'rst',
                'name' => 'RST',
                'priceGross' => 1.0,
            ]
        ]);

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'ijk',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBeNull();

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'lmn',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBeNull();

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'opq',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBe([
            'provider' => 'opq',
            'name' => 'OPQ',
            'priceGross' => 1.0,
        ]);

    post(route('api.shop.cart.paymentMode.store', [
        'provider' => 'rst',
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->paymentMode
        ->toBe([
            'provider' => 'rst',
            'name' => 'RST',
            'priceGross' => 1.0,
        ]);
});
