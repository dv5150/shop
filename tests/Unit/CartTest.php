<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;

class CartTest extends TestCase
{
    protected ProductContract $productA;
    protected ProductContract $productB;
    protected ProductContract $productC; // digital product

    protected function setUp(): void
    {
        parent::setUp();

        $this->productA = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->productB = config('shop.models.product')::factory()
            ->create()
            ->refresh();

        $this->productC = config('shop.models.product')::factory()
            ->create(['is_digital_product' => true])
            ->refresh();
    }

    /** @test */
    public function items_can_be_added()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getKey(),
                    'name' => $this->productA->getName(),
                    'price_gross' => $this->productA->getPriceGross(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getKey(),
                    'name' => $this->productB->getName(),
                    'price_gross' => $this->productB->getPriceGross(),
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

        Cart::addItem($this->productA, 5);
        Cart::addItem($this->productB, 9);

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getKey(),
                    'name' => $this->productA->getName(),
                    'price_gross' => $this->productA->getPriceGross(),
                ],
                'quantity' => 6,
            ],
            [
                'item' => [
                    'id' => $this->productB->getKey(),
                    'name' => $this->productB->getName(),
                    'price_gross' => $this->productB->getPriceGross(),
                ],
                'quantity' => 10,
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
    public function items_can_be_removed()
    {
        Cart::addItem($this->productA, 15);
        Cart::addItem($this->productB, 12);
        Cart::addItem($this->productC, 6);

        Cart::removeItem($this->productA);
        Cart::removeItem($this->productB);
        Cart::removeItem($this->productC);

        $expectedItems = [
            [
                'item' => [
                    'id' => $this->productA->getKey(),
                    'name' => $this->productA->getName(),
                    'price_gross' => $this->productA->getPriceGross(),
                ],
                'quantity' => 14,
            ],
            [
                'item' => [
                    'id' => $this->productB->getKey(),
                    'name' => $this->productB->getName(),
                    'price_gross' => $this->productB->getPriceGross(),
                ],
                'quantity' => 11,
            ],
            [
                'item' => [
                    'id' => $this->productC->getKey(),
                    'name' => $this->productC->getName(),
                    'price_gross' => $this->productC->getPriceGross(),
                ],
                'quantity' => 5,
            ]
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => $expectedItems
                ]
            ]);

        Cart::removeItem($this->productA, 14);
        Cart::removeItem($this->productB, 11);
        Cart::removeItem($this->productC, 999);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [],
                ]
            ]);
    }

    /** @test */
    public function items_can_be_erased()
    {
        Cart::addItem($this->productA, 15);
        Cart::eraseItem($this->productA);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [],
                ]
            ]);
    }

    /** @test */
    public function cart_can_be_recognized_as_digital_cart()
    {
        Cart::addItem($this->productA, 3);
        Cart::addItem($this->productB, 6);
        Cart::addItem($this->productC, 2);

        $this->assertFalse(Cart::hasDigitalItemsOnly());

        Cart::removeItem($this->productA, 3);
        Cart::removeItem($this->productB, 6);

        $this->assertTrue(Cart::hasDigitalItemsOnly());
    }
}
