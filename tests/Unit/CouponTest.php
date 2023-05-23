<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;
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
                    'coupon' => null,
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
                    'coupon' => $couponA->toArray(),
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
                    'coupon' => null,
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
                    'coupon' => $couponB->toArray(),
                ]
            ]);

        $this->delete(route('api.shop.cart.coupon.erase'));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
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
                    'coupon' => null,
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
                    'coupon' => $couponA->toArray(),
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
                    'coupon' => null,
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
                    'coupon' => $couponB->toArray(),
                ]
            ]);

        $this->delete(route('api.shop.cart.coupon.erase'));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $this->expectedProductData,
                    'total' => 28000.0,
                    'coupon' => null,
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
                    'coupon' => $coupon->toArray(),
                ]
            ]);
    }
}
