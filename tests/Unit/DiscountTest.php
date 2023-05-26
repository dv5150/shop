<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Discounts\ProductValueDiscount;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\Concerns\ProvidesSampleOrderData;
use DV5150\Shop\Tests\Concerns\ProvidesSampleProductData;

class DiscountTest extends TestCase
{
    use ProvidesSampleOrderData,
        ProvidesSampleProductData,
        CreatesDiscountsForProducts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleOrderData();
        $this->setUpSampleProductData();

        $this->productA->update(['price_gross' => 5000.0]);
        $this->productB->update(['price_gross' => 7500.0]);
        $this->productC->update(['price_gross' => 12300.0]);
    }

    /** @test */
    public function product_percent_discount_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $discountA = $this->createPercentDiscountForProduct(
            $this->productA,
            '60% OFF discount',
            60.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            $this->productB,
            '13% OFF discount',
            13.0
        );

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2000.0,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 6525.0,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 12300.0,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems,
                ]
            ]);
    }

    /** @test */
    public function product_value_discount_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $discountA = $this->createValueDiscountForProduct(
            $this->productA,
            '1000 OFF discount',
            1000.0
        );

        $discountB = $this->createValueDiscountForProduct(
            $this->productB,
            '4400 OFF discount',
            4400.0
        );

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 4000.0,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 3100.0,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 12300.0,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems
                ]
            ]);
    }

    /** @test */
    public function best_available_discount_is_applied()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $this->createValueDiscountForProduct(
            $this->productA,
            '1000 OFF discount',
            1000.0
        );

        $discountAB = $this->createValueDiscountForProduct(
            $this->productA,
            '3000 OFF discount',
            3000.0
        );

        $this->createPercentDiscountForProduct(
            $this->productB,
            '20% OFF discount',
            20.0
        );

        $discountBB = $this->createPercentDiscountForProduct(
            $this->productB,
            '70% OFF discount',
            70.0
        );

        $this->createPercentDiscountForProduct(
            $this->productC,
            '3% OFF discount',
            3.0
        );

        $discountCB = $this->createValueDiscountForProduct(
            $this->productC,
            '1000 OFF discount',
            1000.0
        );

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2000.0,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 2250.0,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 11300.0,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => $discountCB->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems,
                ]
            ]);
    }

    /** @test */
    public function best_available_price_is_applied_when_removing_a_discount()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);

        $discountAA = $this->createPercentDiscountForProduct($this->productA, '55% OFF discount', 55.0);
        $discountAB = $this->createPercentDiscountForProduct($this->productA, '88% OFF discount', 88.0);

        $discountBA = $this->createValueDiscountForProduct($this->productB, '510 OFF discount', 510.0);
        $discountBB = $this->createValueDiscountForProduct($this->productB, '1510 OFF discount', 1510.0);

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 600.0,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 5990.0,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBB->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems,
                ]
            ]);

        $discountAB->delete();
        $discountBB->delete();

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2250.0,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 6990.0,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBA->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems,
                ]
            ]);

        $discountAA->delete();
        $discountBA->delete();

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => $this->productA->getPriceGross(),
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
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
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems,
                ]
            ]);
    }

    /** @test */
    public function discounted_order_items_are_saved_properly_as_guest()
    {
        $discountA = $this->createValueDiscountForProduct(
            $this->productA,
            '1700 OFF discount',
            1700.0
        );

        $discountB = $this->createPercentDiscountForProduct(
            $this->productB,
            '11% OFF discount',
            11.0
        );

        $this->post(
            route('api.shop.checkout.store'),
            array_merge($this->testOrderData, [
                'cartData' => [
                    [
                        'item' => [
                            'id' => $this->productA->getID(),
                        ],
                        'quantity' => 2,
                    ],
                    [
                        'item' => [
                            'id' => $this->productB->getID(),
                        ],
                        'quantity' => 2,
                    ],
                    [
                        'item' => [
                            'id' => $this->productC->getID(),
                        ],
                        'quantity' => 2,
                    ],
                ],
            ])
        );

        $orderKey = config('shop.models.order')::first()->getKey();

        $this->assertDatabaseHas('orders', array_merge($this->expectedBaseOrderData, [
            'user_id' => null,
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductAData, [
            'order_id' => $orderKey,
            'quantity' => 2,
            'price_gross' => 3300.0,
            'info' => $discountA->getFullname(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductBData, [
            'order_id' => $orderKey,
            'quantity' => 2,
            'price_gross' => 6675.0,
            'info' => $discountB->getFullname(),
        ]));

        $this->assertDatabaseHas('order_items', array_merge($this->expectedProductCData, [
            'order_id' => $orderKey,
            'quantity' => 2,
            'price_gross' => 12300.0,
            'info' => null,
        ]));
    }

    /** @test */
    public function base_discounts_get_deleted_when_product_discounts_are_deleted()
    {
        $this->createValueDiscountForProduct(
            $this->productA,
            '1700 OFF discount',
            1700.0
        );

        $this->createPercentDiscountForProduct(
            $this->productB,
            '11% OFF discount',
            11.0
        );

        $this->assertDatabaseHas('discounts', [
            'discountable_type' => config('shop.models.product'),
            'discountable_id' => $this->productA->getKey(),
            'discount_type' => ProductValueDiscount::class,
            'discount_id' => 1,
        ]);

        $this->assertDatabaseHas('discounts', [
            'discountable_type' => config('shop.models.product'),
            'discountable_id' => $this->productB->getKey(),
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

        ProductValueDiscount::first()->delete();
        ProductPercentDiscount::first()->delete();

        $this->assertDatabaseCount('product_value_discounts', 0);
        $this->assertDatabaseCount('product_percent_discounts', 0);

        $this->assertDatabaseCount('discounts', 0);
    }
}
