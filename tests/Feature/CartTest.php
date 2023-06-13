<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\TestCase;

class CartTest extends TestCase
{
    /** @test */
    public function items_can_be_added()
    {
        $this->post(route('api.shop.cart.store', [
            'product' => $this->productA,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productB,
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart($this->productA),
                        $this->expectProductInCart($this->productB),
                    ],
                ]
            ]);

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productA,
            'quantity' => 5,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productB,
            'quantity' => 9,
        ]));

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
        $this->post(route('api.shop.cart.store', [
            'product' => $this->productA,
            'quantity' => 15,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productB,
            'quantity' => 12,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productC,
            'quantity' => 6,
        ]));

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productA,
        ]));

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productB,
        ]));

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productC,
        ]));

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

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productA,
            'quantity' => 14,
        ]));

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productB,
            'quantity' => 11,
        ]));

        $this->post(route('api.shop.cart.remove', [
            'product' => $this->productC,
            'quantity' => 999,
        ]));

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
        $this->post(route('api.shop.cart.store', [
            'product' => $this->productA,
            'quantity' => 15,
        ]));

        $this->get(route('api.shop.cart.index'))
            ->assertJson([
                'cart' => [
                    'items' => [
                        $this->expectProductInCart($this->productA, 15),
                    ],
                ]
            ]);

        $this->delete(route('api.shop.cart.erase', [
            'product' => $this->productA,
        ]));

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

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productA,
            'quantity' => 3,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productB,
            'quantity' => 6,
        ]));

        $this->post(route('api.shop.cart.store', [
            'product' => $this->productC,
            'quantity' => 2,
        ]));

        $this->assertFalse(Cart::hasDigitalItemsOnly());

        $this->delete(route('api.shop.cart.erase', [
            'product' => $this->productA,
        ]));

        $this->delete(route('api.shop.cart.erase', [
            'product' => $this->productB,
        ]));

        $this->assertTrue(Cart::hasDigitalItemsOnly());
    }
}
