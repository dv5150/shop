<?php

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Concerns\CreatesCartCoupons;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\TestCase;

class CombinedDealTest extends TestCase
{
    use CreatesCartCoupons,
        CreatesDiscountsForProducts;

    /** @test */
    public function percent_coupons_work_together_properly_with_discounts()
    {
        Cart::addItem($this->productA, 2);
        Cart::addItem($this->productB, 5);
        Cart::addItem($this->productC, 3);

        $couponA = $this->createCartPercentCoupon(
            '10% OFF discount', 10.0, 'CART10PERCENTOFF'
        );

        $discountA = $this->createPercentDiscountForProduct(
            $this->productA, '20% OFF discount', 20.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productB, '440 OFF discount', 440.0
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode(),
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productA,
                            quantity: 2,
                            discount: $discountA,
                            overwriteGrossPrice: 400.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productB,
                            quantity: 5,
                            discount: $discountB,
                            overwriteGrossPrice: 1060.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productC,
                            quantity: 3,
                            overwriteGrossPrice: 1800.0
                        ),
                    ],
                    'subtotal' => 11500.0,
                    'total' => 10350.0,
                    'coupon' => [
                        'couponItem' => $couponA->toArray(),
                        'couponDiscountAmount' => -1150.0,
                    ],
                ]
            ]);
    }

    /** @test */
    public function value_coupons_work_together_properly_with_discounts()
    {
        Cart::addItem($this->productA, 2);
        Cart::addItem($this->productB, 5);
        Cart::addItem($this->productC, 3);

        $couponA = $this->createCartValueCoupon(
            '4100 OFF discount', 4100.0, 'CART4100OFF'
        );

        $discountA = $this->createPercentDiscountForProduct(
            $this->productA, '20% OFF discount', 20.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productB, '440 OFF discount', 440.0
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode(),
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productA,
                            quantity: 2,
                            discount: $discountA,
                            overwriteGrossPrice: 400.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productB,
                            quantity: 5,
                            discount: $discountB,
                            overwriteGrossPrice: 1060.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productC,
                            quantity: 3,
                            overwriteGrossPrice: 1800.0
                        ),
                    ],
                    'subtotal' => 11500.0,
                    'total' => 7400.0,
                    'coupon' => [
                        'couponItem' => $couponA->toArray(),
                        'couponDiscountAmount' => -4100.0,
                    ],
                ]
            ]);
    }
}