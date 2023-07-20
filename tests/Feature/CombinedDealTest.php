<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Deals\Coupons\CouponContract;
use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartPercentCoupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupons\CartValueCoupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Discount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductValueDiscount;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('calculates coupons and discounts together properly A', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 28990.0],
            ['price_gross' => 9990.0],
            ['price_gross' => 14370.0],
        ))
        ->create()
        ->all();

    Cart::addItem($productA, 2);
    Cart::addItem($productB, 5);
    Cart::addItem($productC, 3);

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

    /** @var BaseDiscountContract $discountA */
    $discountA = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '20% OFF discount',
            'value' => 20.0,
        ])->getBaseDiscount();

    /** @var BaseDiscountContract $discountB */
    $discountB = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '440 OFF discount',
            'value' => 440.0,
        ])->getBaseDiscount();

    $productA->discounts()->sync($discountA);
    $productB->discounts()->sync($discountB);

    post(route('api.shop.cart.coupon.store', [
        'code' => $couponA->getCode(),
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe([
            'items' => [
                [
                    'item' => [
                        'id' => $productA->getKey(),
                        'name' => $productA->getName(),
                        'price_gross' => 23192.0,
                        'price_gross_original' => 28990.0,
                        'discount' => [
                            'name' => $discountA->getName(),
                            'value' => $discountA->getValue(),
                            'unit' => $discountA->getUnit(),
                        ],
                        'is_digital' => false,
                    ],
                    'quantity' => 2,
                    'subtotal' => 46384.0,
                ],
                [
                    'item' => [
                        'id' => $productB->getKey(),
                        'name' => $productB->getName(),
                        'price_gross' => 9550.0,
                        'price_gross_original' => 9990.0,
                        'discount' => [
                            'name' => $discountB->getName(),
                            'value' => $discountB->getValue(),
                            'unit' => $discountB->getUnit(),
                        ],
                        'is_digital' => false,
                    ],
                    'quantity' => 5,
                    'subtotal' => 47750.0,
                ],
                [
                    'item' => [
                        'id' => $productC->getKey(),
                        'name' => $productC->getName(),
                        'price_gross' => 14370.0,
                        'price_gross_original' => 14370.0,
                        'discount' => null,
                        'is_digital' => false,
                    ],
                    'quantity' => 3,
                    'subtotal' => 43110.0,
                ],
            ],
            'coupon' => [
                'couponItem' => $couponA->toArray(),
                'couponDiscountAmount' => -13725.0,
            ],
            'subtotal' => 137244.0,
            'total' => 123519.0,
            'currency' => [
                'code' => 'HUF'
            ],
            'availableShippingModes' => [],
            'shippingMode' => null,
            'paymentMode' => null,
            'preSavedShippingAddresses' => [],
            'messages' => null,

        ]);
});

it('calculates coupons and discounts together properly B', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 17990.0],
            ['price_gross' => 6990.0],
            ['price_gross' => 10310.0],
        ))
        ->create()
        ->all();

    Cart::addItem($productA, 2);
    Cart::addItem($productB, 5);
    Cart::addItem($productC, 3);

    /** @var BaseCouponContract $couponA */
    $couponA = CartValueCoupon::factory()
        ->afterCreating(function (CouponContract $coupon) {
            /** @var BaseCouponContract $baseCoupon */
            $baseCoupon = Coupon::factory()->make(['code' => 'CART10PERCENTOFF']);
            $baseCoupon->coupon()->associate($coupon);
            $baseCoupon->save();
        })
        ->create([
            'name' => '3000 OFF discount',
            'value' => 3000.0,
        ])->getBaseCoupon();

    /** @var BaseDiscountContract $discountA */
    $discountA = ProductPercentDiscount::factory()
        ->afterCreating(function (ProductPercentDiscount $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '20% OFF discount',
            'value' => 20.0,
        ])->getBaseDiscount();

    /** @var BaseDiscountContract $discountB */
    $discountB = ProductValueDiscount::factory()
        ->afterCreating(function (ProductValueDiscount $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '440 OFF discount',
            'value' => 440.0,
        ])->getBaseDiscount();

    $productA->discounts()->sync($discountA);
    $productB->discounts()->sync($discountB);

    post(route('api.shop.cart.coupon.store', [
        'code' => $couponA->getCode(),
    ]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->toBeJson()
        ->json()
        ->cart
        ->toBe([
            'items' => [
                [
                    'item' => [
                        'id' => $productA->getKey(),
                        'name' => $productA->getName(),
                        'price_gross' => 14392.0,
                        'price_gross_original' => 17990.0,
                        'discount' => [
                            'name' => $discountA->getName(),
                            'value' => $discountA->getValue(),
                            'unit' => $discountA->getUnit(),
                        ],
                        'is_digital' => false,
                    ],
                    'quantity' => 2,
                    'subtotal' => 28784.0,
                ],
                [
                    'item' => [
                        'id' => $productB->getKey(),
                        'name' => $productB->getName(),
                        'price_gross' => 6550.0,
                        'price_gross_original' => 6990.0,
                        'discount' => [
                            'name' => $discountB->getName(),
                            'value' => $discountB->getValue(),
                            'unit' => $discountB->getUnit(),
                        ],
                        'is_digital' => false,
                    ],
                    'quantity' => 5,
                    'subtotal' => 32750.0,
                ],
                [
                    'item' => [
                        'id' => $productC->getKey(),
                        'name' => $productC->getName(),
                        'price_gross' => 10310.0,
                        'price_gross_original' => 10310.0,
                        'discount' => null,
                        'is_digital' => false,
                    ],
                    'quantity' => 3,
                    'subtotal' => 30930.0,
                ],
            ],
            'coupon' => [
                'couponItem' => $couponA->toArray(),
                'couponDiscountAmount' => -3000.0,
            ],
            'subtotal' => 92464.0,
            'total' => 89464.0,
            'currency' => [
                'code' => 'HUF'
            ],
            'availableShippingModes' => [],
            'shippingMode' => null,
            'paymentMode' => null,
            'preSavedShippingAddresses' => [],
            'messages' => null,

        ]);
});