<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\TestCase;

class CartTest extends TestCase
{
    /** @test */
    public function items_can_be_added()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart($this->productA),
                        $this->expectProductInCart($this->productB),
                    ],
                ]
            ]);

        Cart::addItem($this->productA, 5);
        Cart::addItem($this->productB, 9);

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart($this->productA, 6),
                        $this->expectProductInCart($this->productB, 10),
                    ],
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

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart($this->productA, 14),
                        $this->expectProductInCart($this->productB, 11),
                        $this->expectProductInCart($this->productC, 5),
                    ],
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
        $this->productC->update(['is_digital_product' => true]);

        Cart::addItem($this->productA, 3);
        Cart::addItem($this->productB, 6);
        Cart::addItem($this->productC, 2);

        $this->assertFalse(Cart::hasDigitalItemsOnly());

        Cart::eraseItem($this->productA);
        Cart::eraseItem($this->productB);

        $this->assertTrue(Cart::hasDigitalItemsOnly());
    }
}
