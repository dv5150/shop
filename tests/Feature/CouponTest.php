<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Deals\Coupons\CartPercentCoupon;
use DV5150\Shop\Models\Deals\Coupons\CartValueCoupon;
use DV5150\Shop\Tests\Concerns\CreatesCartCoupons;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSamplePaymentModeData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class CouponTest extends TestCase
{
    use CreatesCartCoupons,
        ProvidesSampleOrderData,
        ProvidesSampleShippingModeData,
        ProvidesSamplePaymentModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleShippingModeData();

        $this->productA->update(['price_gross' => 3000.0]);
        $this->productB->update(['price_gross' => 8000.0]);
        $this->productC->update(['price_gross' => 17000.0]);
    }

    /** @test */
    public function cart_percent_coupon_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $couponA = $this->createCartPercentCoupon(
            name: '10% OFF discount',
            value: 10.0,
            code: 'CART10PERCENTOFF'
        );

        $couponB = $this->createCartPercentCoupon(
            name: '25% OFF discount',
            value: 25.0,
            code: 'CART25PERCENTOFF'
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(sellableItem: $this->productA),
                        $this->expectProductInCart(sellableItem: $this->productB),
                        $this->expectProductInCart(sellableItem: $this->productC),
                    ],
                    'total' => 28000.0,
                    'coupon' => null,
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode(),
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 25200.0,
                'coupon' => [
                    'couponItem' => $couponA->toArray(),
                    'couponDiscountAmount' => -2800.0,
                ],
            ]
        ]);

        // expect no change compared to previous one when non-existent code is tried
        $this->post(route('api.shop.cart.coupon.store', [
            'code' => 'non-existent-code'
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 25200.0,
                'coupon' => [
                    'couponItem' => $couponA->toArray(),
                    'couponDiscountAmount' => -2800.0,
                ],
                'messages' => [
                    'coupon' => [
                        '404' => [
                            'text' => __('Coupon not found.'),
                            'type' => 'negative',
                        ],
                    ]
                ]
            ],
        ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponB->getCode(),
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 21000.0,
                'coupon' => [
                    'couponItem' => $couponB->toArray(),
                    'couponDiscountAmount' => -7000.0,
                ],
            ]
        ]);

        $this->delete(route('api.shop.cart.coupon.erase'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(sellableItem: $this->productA),
                        $this->expectProductInCart(sellableItem: $this->productB),
                        $this->expectProductInCart(sellableItem: $this->productC),
                    ],
                    'total' => 28000.0,
                    'coupon' => null,
                ]
            ]);
    }

    /** @test */
    public function cart_value_coupon_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $couponA = $this->createCartValueCoupon(
            name: '700 OFF discount',
            value: 700.0,
            code: 'CART700OFF'
        );

        $couponB = $this->createCartValueCoupon(
            name: '1900 OFF discount',
            value: 1900.0,
            code: 'CART1900FF'
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(sellableItem: $this->productA),
                        $this->expectProductInCart(sellableItem: $this->productB),
                        $this->expectProductInCart(sellableItem: $this->productC),
                    ],
                    'total' => 28000.0,
                    'coupon' => null,
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode()
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 27300.0,
                'coupon' => [
                    'couponItem' => $couponA->toArray(),
                    'couponDiscountAmount' => -700.0,
                ],
            ]
        ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => 'non-existent-code'
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 27300.0,
                'coupon' => [
                    'couponItem' => $couponA->toArray(),
                    'couponDiscountAmount' => -700.0,
                ],
                'messages' => [
                    'coupon' => [
                        '404' => [
                            'text' => __('Coupon not found.'),
                            'type' => 'negative',
                        ],
                    ]
                ]
            ]
        ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponB->getCode(),
        ]))->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 26100.0,
                'coupon' => [
                    'couponItem' => $couponB->toArray(),
                    'couponDiscountAmount' => -1900.0,
                ],
            ]
        ]);

        $this->delete(route('api.shop.cart.coupon.erase'))
            ->assertJson([
            'cart' => [
                'items' => [
                    $this->expectProductInCart(sellableItem: $this->productA),
                    $this->expectProductInCart(sellableItem: $this->productB),
                    $this->expectProductInCart(sellableItem: $this->productC),
                ],
                'total' => 28000.0,
                'coupon' => null,
            ]
        ]);
    }

    /** @test */
    public function cart_value_coupons_are_saved_properly_as_order_items_as_guest()
    {
        $coupon = $this->createCartValueCoupon(
            name: '1100 OFF discount',
            value: 1100.0,
            code: 'CART1100OFF'
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $coupon->getCode(),
        ]));

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 7),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 7),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingMode->getProvider(),
                ],
                'paymentMode' => [
                    'provider' => $this->shippingMode->paymentModes()
                        ->first()
                        ->getProvider(),
                ],
                'shipping_mode_provider' => $this->shippingMode->getProvider(),
                'payment_mode_provider' => $this->shippingMode->paymentModes()
                    ->first()
                    ->getProvider(),
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productA, order: $order, quantity: 7);
        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productB, order: $order, quantity: 7);

        $this->assertDatabaseHasCouponOrderItem(
            coupon: $coupon,
            order: $order,
            priceGross: -1100.0,
            info: "Code: {$coupon->getCode()}"
        );
    }

    /** @test */
    public function cart_percent_coupons_are_saved_properly_as_order_items_as_guest()
    {
        $coupon = $this->createCartPercentCoupon(
            name: '25% OFF discount',
            value: 25.0,
            code: 'CART25OFF'
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $coupon->getCode(),
        ]));

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem(sellableItem: $this->productA, quantity: 8),
                    $this->makeProductCartDataItem(sellableItem: $this->productB, quantity: 8),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingMode->getProvider(),
                ],
                'paymentMode' => [
                    'provider' => $this->shippingMode->paymentModes()
                        ->first()
                        ->getProvider(),
                ],
                'shipping_mode_provider' => $this->shippingMode->getProvider(),
                'payment_mode_provider' => $this->shippingMode->paymentModes()
                    ->first()
                    ->getProvider(),
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productA, order: $order, quantity: 8);
        $this->assertDatabaseHasProductOrderItem(sellableItem: $this->productB, order: $order, quantity: 8);

        $itemsTotal = array_sum([
            $this->productA->getPriceGross() * 8,
            $this->productB->getPriceGross() * 8,
        ]);

        $this->assertDatabaseHasCouponOrderItem(
            coupon: $coupon,
            order: $order,
            priceGross: 0 - ($itemsTotal * 0.25),
            info: "Code: {$coupon->getCode()}"
        );
    }

    /** @test */
    public function coupons_get_deleted_when_base_coupons_are_deleted()
    {
        $baseCouponA = $this->createCartValueCoupon(
            name: '100 OFF discount',
            value: 100.0,
            code: 'CART100'
        );

        $baseCouponB = $this->createCartPercentCoupon(
            name: '40% OFF discount',
            value: 40.0,
            code: 'CART40'
        );

        $this->assertDatabaseHas('coupons', [
            'coupon_type' => CartValueCoupon::class,
            'coupon_id' => 1,
        ]);

        $this->assertDatabaseHas('coupons', [
            'coupon_type' => CartPercentCoupon::class,
            'coupon_id' => 1,
        ]);

        $this->assertDatabaseHas('cart_value_coupons', [
            'name' => '100 OFF discount',
            'value' => 100.0,
        ]);

        $this->assertDatabaseHas('cart_percent_coupons', [
            'name' => '40% OFF discount',
            'value' => 40.0,
        ]);

        $baseCouponA->delete();
        $baseCouponB->delete();

        $this->assertDatabaseCount('cart_value_coupons', 0);
        $this->assertDatabaseCount('cart_percent_coupons', 0);

        $this->assertDatabaseCount('coupons', 0);
    }

    /** @test */
    public function coupon_code_attribute_being_saved_as_uppercase_text()
    {
        $coupon = $this->createCartPercentCoupon(
            name: '10% OFF discount',
            value: 10.0,
            code: 'cartdiscount'
        );

        $savedCode = $coupon->refresh()->getCode();

        $this->assertSame('CARTDISCOUNT', $savedCode);
        $this->assertNotSame('cartdiscount', $savedCode);
    }
}
