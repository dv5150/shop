<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Coupons\CartPercentCoupon;
use DV5150\Shop\Models\Coupons\CartValueCoupon;
use DV5150\Shop\Tests\Concerns\CreatesCartCoupons;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;

class CouponTest extends TestCase
{
    use ProvidesSampleOrderData,
        CreatesCartCoupons,
        CreatesDiscountsForProducts;

    protected ProductContract $productA;
    protected ProductContract $productB;
    protected ProductContract $productC;

    protected array $expectedProductData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();

        $this->productA = config('shop.models.product')::factory()
            ->create(['price_gross' => 3000.0])
            ->refresh();

        $this->productB = config('shop.models.product')::factory()
            ->create(['price_gross' => 8000.0])
            ->refresh();

        $this->productC = config('shop.models.product')::factory()
            ->create(['price_gross' => 17000.0])
            ->refresh();

        $this->expectedProductData = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => $this->productA->getPriceGross(),
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
                'subtotal' => $this->productA->getPriceGross(),
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => $this->productB->getPriceGross(),
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
                'subtotal' => $this->productB->getPriceGross(),
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => $this->productC->getPriceGross(),
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
                'subtotal' => $this->productC->getPriceGross(),
            ],
        ];
    }

    /** @test */
    public function cart_percent_coupon_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $couponA = $this->createCartPercentCoupon(
            '10% OFF discount',
            10.0,
            'CART10PERCENTOFF'
        );

        $couponB = $this->createCartPercentCoupon(
            '25% OFF discount',
            25.0,
            'CART25PERCENTOFF'
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->code
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 25200.0,
                    'coupon' => [
                        'couponItem' => $couponA->toArray(),
                        'couponDiscountAmount' => -2800.0,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => 'non-existent-code'
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponB->code
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 21000.0,
                    'coupon' => [
                        'couponItem' => $couponB->toArray(),
                        'couponDiscountAmount' => -7000.0,
                    ],
                ]
            ]);

        $this->delete(route('api.shop.cart.coupon.erase'));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
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
            '700 OFF discount',
            700.0,
            'CART700OFF'
        );

        $couponB = $this->createCartValueCoupon(
            '1900 OFF discount',
            1900.0,
            'CART1900FF'
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->code
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 27300.0,
                    'coupon' => [
                        'couponItem' => $couponA->toArray(),
                        'couponDiscountAmount' => -700.0,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => 'non-existent-code'
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponB->code
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 26100.0,
                    'coupon' => [
                        'couponItem' => $couponB->toArray(),
                        'couponDiscountAmount' => -1900.0,
                    ],
                ]
            ]);

        $this->delete(route('api.shop.cart.coupon.erase'));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => [
                        'couponItem' => null,
                        'couponDiscountAmount' => null,
                    ],
                ]
            ]);
    }

    /** @test */
    public function coupons_work_together_properly_with_discounts()
    {
        Cart::addItem($this->productA, 3);
        Cart::addItem($this->productB, 5);
        Cart::addItem($this->productC, 2);

        $discountA = $this->createPercentDiscountForProduct(
            $this->productA,
            '50% OFF discount',
            50.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            $this->productB,
            '40% OFF discount',
            40.0
        );

        $coupon = $this->createCartValueCoupon(
            '660 OFF discount',
            660.0,
            'CART660OFF'
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $coupon->code
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        [
                            'item' => [
                                'id' => $this->productA->getID(),
                                'name' => $this->productA->getName(),
                                'price_gross' => 1500.0,
                                'price_gross_original' => $this->productA->getPriceGross(),
                                'discount' => $discountA->toArray(),
                            ],
                            'quantity' => 3,
                            'subtotal' => 4500.0,
                        ],
                        [
                            'item' => [
                                'id' => $this->productB->getID(),
                                'name' => $this->productB->getName(),
                                'price_gross' => 4800.0,
                                'price_gross_original' => $this->productB->getPriceGross(),
                                'discount' => $discountB->toArray(),
                            ],
                            'quantity' => 5,
                            'subtotal' => 24000.0,
                        ],
                        [
                            'item' => [
                                'id' => $this->productC->getID(),
                                'name' => $this->productC->getName(),
                                'price_gross' => 17000.0,
                                'price_gross_original' => $this->productC->getPriceGross(),
                                'discount' => null,
                            ],
                            'quantity' => 2,
                            'subtotal' => 34000.0,
                        ],
                    ],
                    'total' => 4500.0 + 24000.0 + 34000.0 - 660.0,
                    'coupon' => [
                        'couponItem' => $coupon->toArray(),
                        'couponDiscountAmount' => -660.0,
                    ],
                ]
            ]);
    }

    /** @test */
    public function cart_value_coupons_are_saved_properly_as_order_items_as_guest()
    {
        $discountA = $this->createPercentDiscountForProduct(
            $this->productA,
            '50% OFF discount',
            50.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productB,
            '900 OFF discount',
            900.0
        );

        $coupon = $this->createCartValueCoupon(
            '1100 OFF discount',
            1100.0,
            'CART1100OFF'
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $coupon->code
        ]));

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getID(),
                        ],
                        'quantity' => 7,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 7,
                    ],
                ],
            ])
        );

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge([
            'product_id' => $this->productA->getID(),
            'name' => $this->productA->getName(),
        ], [
            'order_id' => $orderKey,
            'quantity' => 7,
            'price_gross' => 1500.0,
            'info' => $discountA->getFullname(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge([
            'product_id' => $this->productB->getID(),
            'name' => $this->productB->getName(),
        ], [
            'order_id' => $orderKey,
            'quantity' => 7,
            'price_gross' => 7100.0,
            'info' => $discountB->getFullname(),
        ]));

        $this->assertDatabaseHas('order_items', [
            'product_id' => null,
            'name' => $coupon->getFullName(),
            'order_id' => $orderKey,
            'quantity' => 1,
            'price_gross' => -1100.0,
            'info' => null,
        ]);
    }

    /** @test */
    public function cart_percent_coupons_are_saved_properly_as_order_items_as_guest()
    {
        $discountA = $this->createPercentDiscountForProduct(
            $this->productA,
            '30% OFF discount',
            30.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productB,
            '500 OFF discount',
            500.0
        );

        $coupon = $this->createCartPercentCoupon(
            '25% OFF discount',
            25.0,
            'CART25OFF'
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $coupon->code
        ]));

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getID(),
                        ],
                        'quantity' => 8,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 8,
                    ],
                ],
            ])
        );

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge([
            'product_id' => $this->productA->getID(),
            'name' => $this->productA->getName(),
        ], [
            'order_id' => $orderKey,
            'quantity' => 8,
            'price_gross' => 2100.0,
            'info' => $discountA->getFullname(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge([
            'product_id' => $this->productB->getID(),
            'name' => $this->productB->getName(),
        ], [
            'order_id' => $orderKey,
            'quantity' => 8,
            'price_gross' => 7500.0,
            'info' => $discountB->getFullname(),
        ]));

        $itemsTotal = array_sum([
            2100.0 * 8,
            7500.0 * 8,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => null,
            'name' => $coupon->getFullName(),
            'order_id' => $orderKey,
            'quantity' => 1,
            'price_gross' => 0 - ($itemsTotal * 0.25),
            'info' => null,
        ]);
    }

    /** @test */
    public function base_coupons_get_deleted_when_cart_coupons_are_deleted()
    {
        $this->createCartValueCoupon(
            '100 OFF discount',
            100.0,
            'CART100'
        );

        $this->createCartPercentCoupon(
            '40% OFF discount',
            40.0,
            'CART40'
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

        CartValueCoupon::first()->delete();
        CartPercentCoupon::first()->delete();

        $this->assertDatabaseCount('cart_value_coupons', 0);
        $this->assertDatabaseCount('cart_percent_coupons', 0);

        $this->assertDatabaseCount('coupons', 0);
    }
}
