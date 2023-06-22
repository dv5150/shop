<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleShippingModeData;
use DV5150\Shop\Tests\TestCase;

class DiscountTest extends TestCase
{
    use CreatesDiscountsForProducts,
        ProvidesSampleOrderData,
        ProvidesSampleShippingModeData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleShippingModeData();

        $this->productC->update(['price_gross' => 5000.0]);
        $this->productD->update(['price_gross' => 7500.0]);
        $this->productE->update(['price_gross' => 12300.0]);
    }

    /** @test */
    public function product_percent_discount_is_working()
    {
        Cart::addItem($this->productC);
        Cart::addItem($this->productD);
        Cart::addItem($this->productE);

        $discountA = $this->createPercentDiscountForProduct(
            $this->productC,
            '60% OFF discount',
            60.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            $this->productD,
            '13% OFF discount',
            13.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            discount: $discountA,
                            overwriteGrossPrice: 2000.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            discount: $discountB,
                            overwriteGrossPrice: 6525.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productE,
                            overwriteGrossPrice: 12300.0,
                        ),
                    ],
                ],
            ]);
    }

    /** @test */
    public function product_value_discount_is_working()
    {
        Cart::addItem($this->productC);
        Cart::addItem($this->productD);
        Cart::addItem($this->productE);

        $discountA = $this->createValueDiscountForProduct(
            $this->productC, '1000 OFF discount', 1000.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productD, '4400 OFF discount', 4400.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            discount: $discountA,
                            overwriteGrossPrice: 4000.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            discount: $discountB,
                            overwriteGrossPrice: 3100.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productE,
                            overwriteGrossPrice: 12300.0,
                        ),
                    ],
                ],
            ]);
    }

    /** @test */
    public function best_available_discount_is_applied()
    {
        Cart::addItem($this->productC);
        Cart::addItem($this->productD);
        Cart::addItem($this->productE);

        $this->createValueDiscountForProduct(
            $this->productC,
            '1000 OFF discount',
            1000.0
        );

        $discountAB = $this->createValueDiscountForProduct(
            $this->productC,
            '3000 OFF discount',
            3000.0
        );

        $this->createPercentDiscountForProduct(
            $this->productD,
            '20% OFF discount',
            20.0
        );

        $discountBB = $this->createPercentDiscountForProduct(
            $this->productD,
            '70% OFF discount',
            70.0
        );

        $this->createPercentDiscountForProduct(
            $this->productE,
            '3% OFF discount',
            3.0
        );

        $discountCB = $this->createValueDiscountForProduct(
            $this->productE,
            '1000 OFF discount',
            1000.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            discount: $discountAB,
                            overwriteGrossPrice: 2000.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            discount: $discountBB,
                            overwriteGrossPrice: 2250.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productE,
                            discount: $discountCB,
                            overwriteGrossPrice: 11300.0,
                        ),
                    ],
                ],
            ]);
    }

    /** @test */
    public function best_available_price_is_applied_when_removing_a_discount()
    {
        Cart::addItem($this->productC);
        Cart::addItem($this->productD);

        $discountAA = $this->createPercentDiscountForProduct(
            $this->productC, '55% OFF discount', 55.0
        );

        $discountAB = $this->createPercentDiscountForProduct(
            $this->productC, '88% OFF discount', 88.0
        );

        $discountBA = $this->createValueDiscountForProduct(
            $this->productD, '510 OFF discount', 510.0
        );

        $discountBB = $this->createValueDiscountForProduct(
            $this->productD, '1510 OFF discount', 1510.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            discount: $discountAB,
                            overwriteGrossPrice: 600.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            discount: $discountBB,
                            overwriteGrossPrice: 5990.0,
                        ),
                    ],
                ]
            ]);

        $discountAB->delete();
        $discountBB->delete();

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            discount: $discountAA,
                            overwriteGrossPrice: 2250.0,
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            discount: $discountBA,
                            overwriteGrossPrice: 6990.0,
                        ),
                    ],
                ]
            ]);

        $discountAA->delete();
        $discountBA->delete();

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            product: $this->productC,
                            overwriteGrossPrice: $this->productC->getPriceGross(),
                        ),
                        $this->expectProductInCart(
                            product: $this->productD,
                            overwriteGrossPrice: $this->productD->getPriceGross(),
                        ),
                    ],
                ]
            ]);
    }

    /** @test */
    public function discounted_order_items_are_saved_properly_as_guest()
    {
        $discountA = $this->createValueDiscountForProduct(
            $this->productC, '1700 OFF discount', 1700.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            $this->productD, '11% OFF discount', 11.0
        );

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    $this->makeProductCartDataItem($this->productC, 2),
                    $this->makeProductCartDataItem($this->productD, 2),
                    $this->makeProductCartDataItem($this->productE, 2),
                ],
                'shippingMode' => [
                    'provider' => $this->shippingModeProvider,
                ],
                'paymentMode' => [
                    'provider' => $this->paymentModeProvider,
                ],
                'shipping_mode_provider' => $this->shippingModeProvider,
                'payment_mode_provider' => $this->paymentModeProvider,
            ])
        );

        $order = config('shop.models.order')::first();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHasProductOrderItem(
            product: $this->productC,
            order: $order,
            quantity: 2,
            info: $discountA->getName(),
            overwriteGrossPrice: 3300.0,
        );

        $this->assertDatabaseHasProductOrderItem(
            product: $this->productD,
            order: $order,
            quantity: 2,
            info: $discountB->getName(),
            overwriteGrossPrice: 6675.0,
        );

        $this->assertDatabaseHasProductOrderItem(
            product: $this->productE,
            order: $order,
            quantity: 2,
            overwriteGrossPrice: 12300.0,
        );
    }

    /** @test */
    public function discounts_get_deleted_when_base_product_discounts_are_deleted()
    {
        $baseDiscountA = $this->createValueDiscountForProduct(
            $this->productC,
            '1700 OFF discount',
            1700.0
        );

        $baseDiscountB = $this->createPercentDiscountForProduct(
            $this->productD,
            '11% OFF discount',
            11.0
        );

        $this->assertDatabaseHas('discounts', [
            'discountable_type' => config('shop.models.product'),
            'discountable_id' => $this->productC->getKey(),
            'discount_type' => ProductValueDiscount::class,
            'discount_id' => 1,
        ]);

        $this->assertDatabaseHas('discounts', [
            'discountable_type' => config('shop.models.product'),
            'discountable_id' => $this->productD->getKey(),
            'discount_type' => ProductPercentDiscount::class,
            'discount_id' => 1,
        ]);

        $this->assertDatabaseHas('product_value_discounts', [
            'name' => '1700 OFF discount',
            'value' => 1700.0,
        ]);

        $this->assertDatabaseHas('product_percent_discounts', [
            'name' => '11% OFF discount',
            'value' => 11.0,
        ]);

        $baseDiscountA->delete();
        $baseDiscountB->delete();

        $this->assertDatabaseCount('product_value_discounts', 0);
        $this->assertDatabaseCount('product_percent_discounts', 0);

        $this->assertDatabaseCount('discounts', 0);
    }
}
