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
            name: '10% OFF discount',
            value: 10.0,
            code: 'CART10PERCENTOFF'
        );

        $discountA = $this->createPercentDiscountForProduct(
            sellableItem: $this->productA,
            name: '20% OFF discount',
            value: 20.0
        );

        $discountB = $this->createValueDiscountForProduct(
            sellableItem: $this->productB,
            name: '440 OFF discount',
            value: 440.0
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode(),
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            sellableItem: $this->productA,
                            quantity: 2,
                            discount: $discountA,
                            overwriteGrossPrice: 400.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productB,
                            quantity: 5,
                            discount: $discountB,
                            overwriteGrossPrice: 1060.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productC,
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
            name: '4100 OFF discount',
            value: 4100.0,
            code: 'CART4100OFF'
        );

        $discountA = $this->createPercentDiscountForProduct(
            sellableItem: $this->productA,
            name: '20% OFF discount',
            value: 20.0
        );

        $discountB = $this->createValueDiscountForProduct(
            sellableItem: $this->productB,
            name: '440 OFF discount',
            value: 440.0
        );

        $this->post(route('api.shop.cart.coupon.store', [
            'code' => $couponA->getCode(),
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            sellableItem: $this->productA,
                            quantity: 2,
                            discount: $discountA,
                            overwriteGrossPrice: 400.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productB,
                            quantity: 5,
                            discount: $discountB,
                            overwriteGrossPrice: 1060.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productC,
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