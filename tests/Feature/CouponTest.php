<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Deals\Coupons\CouponContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartPercentCoupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartValueCoupon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;


it('calculates coupon cart prices properly', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 3000.0],
            ['price_gross' => 8000.0],
            ['price_gross' => 17000.0],
        ))
        ->create()
        ->all();

    $response = [
        'items' => [
            [
                'item' => [
                    'id' => $productA->getKey(),
                    'name' => $productA->getName(),
                    'price_gross' => 3000.0,
                    'price_gross_original' => 3000.0,
                    'discount' => null,
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 3000.0,
            ],
            [
                'item' => [
                    'id' => $productB->getKey(),
                    'name' => $productB->getName(),
                    'price_gross' => 8000.0,
                    'price_gross_original' => 8000.0,
                    'discount' => null,
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 8000.0,
            ],
            [
                'item' => [
                    'id' => $productC->getKey(),
                    'name' => $productC->getName(),
                    'price_gross' => 17000.0,
                    'price_gross_original' => 17000.0,
                    'discount' => null,
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 17000.0,
            ],
        ],
        'coupon' => null,
        'subtotal' => 28000.0,
        'total' => 28000.0,
        'currency' => [
            'code' => 'HUF'
        ],
        'availableShippingModes' => [],
        'shippingMode' => null,
        'paymentMode' => null,
        'preSavedShippingAddresses' => [],
        'messages' => null,
    ];

    Cart::addItem($productA);
    Cart::addItem($productB);
    Cart::addItem($productC);

    /** @var BaseCouponContract $couponA */
    $couponA = CartPercentCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART10PERCENTOFF']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '10% OFF discount',
            'value' => 10.0,
        ])->getBaseCoupon();

    /** @var BaseCouponContract $couponA */
    $couponB = CartPercentCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART25PERCENTOFF']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '25% OFF discount',
            'value' => 25.0,
        ])->getBaseCoupon();

    /** @var BaseCouponContract $couponA */
    $couponC = CartValueCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART3777OFF']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '3777 OFF discount',
            'value' => 3777.0,
        ])->getBaseCoupon();

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);

    // 1) 10% OFF
    Arr::set($response, 'total', 25200.0);
    Arr::set($response, 'coupon', [
        'couponItem' => $couponA->toArray(),
        'couponDiscountAmount' => -2800.0,
    ]);

    expect(post(route('api.shop.cart.coupon.store', [
        'code' => $couponA->getCode(),
    ]))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);

    // 2) expect no change compared to previous one when non-existent code is tried
    Arr::set($response, 'messages', [
        'coupon' => [
            '404' => [
                'type' => 'negative',
                'text' => __('Coupon not found.'),
            ],
        ]
    ]);

    expect(post(route('api.shop.cart.coupon.store', [
        'code' => 'non-existent-code',
    ]))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);

    // 3) 25% OFF
    Arr::set($response, 'total', 21000.0);
    Arr::set($response, 'coupon', [
        'couponItem' => $couponB->toArray(),
        'couponDiscountAmount' => -7000.0,
    ]);
    Arr::set($response, 'messages', null);

    expect(post(route('api.shop.cart.coupon.store', [
        'code' => $couponB->getCode(),
    ]))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);

    // 4) remove coupon
    Arr::set($response, 'total', 28000.0);
    Arr::set($response, 'coupon', null);
    Arr::set($response, 'messages', null);

    expect(delete(route('api.shop.cart.coupon.erase'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);

    // 5) 3777 OFF
    Arr::set($response, 'total', 24223.0);
    Arr::set($response, 'coupon', [
        'couponItem' => $couponC->toArray(),
        'couponDiscountAmount' => -3777.0,
    ]);

    expect(post(route('api.shop.cart.coupon.store', [
        'code' => $couponC->getCode(),
    ]))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe($response);
});

it('saves coupons properly as order items as a guest', function (array $data) {
    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    /** @var CouponContract $coupon */
    list($orderData, $coupon, $expectedDiscountValue) = array_values($data);

    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     */
    list($productA, $productB) = $this->productClass::factory()
        ->count(2)
        ->state(new Sequence(
            ['price_gross' => 3000.0],
            ['price_gross' => 8000.0],
        ))
        ->create()
        ->all();

    post(route('api.shop.cart.coupon.store', [
        'code' => $coupon->getCode(),
    ]));

    post(route('api.shop.checkout.store'), array_merge($orderData, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 7,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 7,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('orders', array_merge($this->expectedOrderDataRequired, [
        'user_id' => null,
    ]));

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'name' => $coupon->getName(),
        'quantity' => 1,
        'price_gross' => $expectedDiscountValue,
        'type' => 'coupon',
        'sellable_type' => $coupon::class,
        'sellable_id' => $coupon->getKey(),
        'info' => "Code: {$coupon->getCode()}",
    ]);
})->with([
    fn () => [
        'orderData' => $this->testOrderDataRequired,
        'coupon' => CartValueCoupon::factory()
            ->afterCreating(function (CouponContract $coupon) {
                /** @var BaseCouponContract $baseCoupon */
                $baseCoupon = Coupon::factory()->make(['code' => 'CART1100OFF']);
                $baseCoupon->coupon()->associate($coupon);
                $baseCoupon->save();
            })
            ->create([
                'name' => '1100 OFF discount',
                'value' => 1100.0,
            ])->getBaseCoupon(),
        'expectedDiscountValue' => -1100.0,
    ],
    fn () => [
        'orderData' => $this->testOrderDataRequired,
        'coupon' => CartPercentCoupon::factory()
            ->afterCreating(function (CouponContract $coupon) {
                /** @var BaseCouponContract $baseCoupon */
                $baseCoupon = Coupon::factory()->make(['code' => 'CART50PERCENTOFF']);
                $baseCoupon->coupon()->associate($coupon);
                $baseCoupon->save();
            })
            ->create([
                'name' => '50% OFF discount',
                'value' => 50.0,
            ])->getBaseCoupon(),
        'expectedDiscountValue' => -38500.0,
    ],
]);

it('deletes child coupons when base coupons being deleted', function (BaseCouponContract $baseCoupon) {
    $coupon = $baseCoupon->getCoupon();

    $this->assertDatabaseHas('coupons', [
        'coupon_type' => $coupon::class,
        'coupon_id' => $coupon->getKey(),
        'code' => $baseCoupon->getCode(),
    ]);

    $this->assertDatabaseHas($coupon->getTable(), [
        'name' => $coupon->getName(),
        'value' => $coupon->getValue(),
    ]);

    $baseCoupon->delete();

    $this->assertDatabaseCount($coupon->getTable(), 0);

    $this->assertDatabaseCount('coupons', 0);
})->with([
    fn () => CartValueCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART100']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '100 OFF discount',
            'value' => 100.0,
        ])->getBaseCoupon(),
    fn () => CartPercentCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART40']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '40% OFF discount',
            'value' => 40.0,
        ])->getBaseCoupon(),
]);

it('saves coupon codes as uppercase text', function (array $data) {
    /**
     * @var BaseCouponContract $coupon
     * @var string $code
     */
    list ($coupon, $code) = array_values($data);

    expect($coupon->refresh()->getCode())
        ->toBe(Str::upper($code))
        ->not()
        ->toBe($code);
})->with([
    fn () => [
        'coupon' => CartValueCoupon::factory()
            ->afterCreating(function (CouponContract $coupon) {
                /** @var BaseCouponContract $baseCoupon */
                $baseCoupon = Coupon::factory()->make(['code' => 'lowercase100']);
                $baseCoupon->coupon()->associate($coupon);
                $baseCoupon->save();
            })
            ->create([
                'name' => '100 OFF discount',
                'value' => 100.0,
            ])->getBaseCoupon(),
        'code' => 'lowercase100',
    ],
    fn () => [
        'coupon' => CartPercentCoupon::factory()
            ->afterCreating(function (CouponContract $coupon) {
                /** @var BaseCouponContract $baseCoupon */
                $baseCoupon = Coupon::factory()->make(['code' => 'lowercase80']);
                $baseCoupon->coupon()->associate($coupon);
                $baseCoupon->save();
            })
            ->create([
                'name' => '80% OFF discount',
                'value' => 80.0,
            ])->getBaseCoupon(),
        'code' => 'lowercase80',
    ],
]);
