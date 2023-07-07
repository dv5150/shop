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
                            sellableItem: $this->productC,
                            discount: $discountA,
                            overwriteGrossPrice: 2000.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
                            discount: $discountB,
                            overwriteGrossPrice: 6525.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productE,
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
            sellableItem: $this->productC,
            name: '1000 OFF discount',
            value: 1000.0
        );

        $discountB = $this->createValueDiscountForProduct(
            sellableItem: $this->productD,
            name: '4400 OFF discount',
            value: 4400.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            sellableItem: $this->productC,
                            discount: $discountA,
                            overwriteGrossPrice: 4000.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
                            discount: $discountB,
                            overwriteGrossPrice: 3100.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productE,
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
            sellableItem: $this->productC,
            name: '1000 OFF discount',
            value: 1000.0
        );

        $discountAB = $this->createValueDiscountForProduct(
            sellableItem: $this->productC,
            name: '3000 OFF discount',
            value: 3000.0
        );

        $this->createPercentDiscountForProduct(
            sellableItem: $this->productD,
            name: '20% OFF discount',
            value: 20.0
        );

        $discountBB = $this->createPercentDiscountForProduct(
            sellableItem: $this->productD,
            name: '70% OFF discount',
            value: 70.0
        );

        $this->createPercentDiscountForProduct(
            sellableItem: $this->productE,
            name: '3% OFF discount',
            value: 3.0
        );

        $discountCB = $this->createValueDiscountForProduct(
            sellableItem: $this->productE,
            name: '1000 OFF discount',
            value: 1000.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            sellableItem: $this->productC,
                            discount: $discountAB,
                            overwriteGrossPrice: 2000.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
                            discount: $discountBB,
                            overwriteGrossPrice: 2250.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productE,
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
            sellableItem: $this->productC,
            name: '55% OFF discount',
            value: 55.0
        );

        $discountAB = $this->createPercentDiscountForProduct(
            sellableItem: $this->productC,
            name: '88% OFF discount',
            value: 88.0
        );

        $discountBA = $this->createValueDiscountForProduct(
            sellableItem: $this->productD,
            name: '510 OFF discount',
            value: 510.0
        );

        $discountBB = $this->createValueDiscountForProduct(
            sellableItem: $this->productD,
            name: '1510 OFF discount',
            value: 1510.0
        );

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart(
                            sellableItem: $this->productC,
                            discount: $discountAB,
                            overwriteGrossPrice: 600.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
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
                            sellableItem: $this->productC,
                            discount: $discountAA,
                            overwriteGrossPrice: 2250.0,
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
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
                            sellableItem: $this->productC,
                            overwriteGrossPrice: $this->productC->getPriceGross(),
                        ),
                        $this->expectProductInCart(
                            sellableItem: $this->productD,
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
            sellableItem: $this->productC,
            name: '1700 OFF discount',
            value: 1700.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            sellableItem: $this->productD,
            name: '11% OFF discount',
            value: 11.0
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
            sellableItem: $this->productC,
            order: $order,
            quantity: 2,
            info: $discountA->getName(),
            overwriteGrossPrice: 3300.0,
        );

        $this->assertDatabaseHasProductOrderItem(
            sellableItem: $this->productD,
            order: $order,
            quantity: 2,
            info: $discountB->getName(),
            overwriteGrossPrice: 6675.0,
        );

        $this->assertDatabaseHasProductOrderItem(
            sellableItem: $this->productE,
            order: $order,
            quantity: 2,
            overwriteGrossPrice: 12300.0,
        );
    }

    /** @test */
    public function discounts_get_deleted_when_base_product_discounts_are_deleted()
    {
        $baseDiscountA = $this->createValueDiscountForProduct(
            sellableItem: $this->productC,
            name: '1700 OFF discount',
            value: 1700.0
        );

        $baseDiscountB = $this->createPercentDiscountForProduct(
            sellableItem: $this->productD,
            name: '11% OFF discount',
            value: 11.0
        );

        $this->assertDatabaseHas('discounts', [
            'discount_type' => ProductValueDiscount::class,
            'discount_id' => 1,
        ]);

        $this->assertDatabaseHas('discounts', [
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
