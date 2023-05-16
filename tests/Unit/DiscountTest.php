<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Discount;
use DV5150\Shop\Models\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Discounts\ProductValueDiscount;

class DiscountTest extends TestCase
{
    protected ProductContract $productA;
    protected ProductContract $productB;
    protected ProductContract $productC;

    protected ProductValueDiscount $valueDiscountA; // 1000 off
    protected ProductValueDiscount $valueDiscountB; // 1700 off

    protected function setUp(): void
    {
        parent::setUp();

        $this->productA = config('shop.models.product')::factory()
            ->create(['price_gross' => 5000])
            ->refresh();

        $this->productB = config('shop.models.product')::factory()
            ->create(['price_gross' => 7500])
            ->refresh();

        $this->productC = config('shop.models.product')::factory()
            ->create(['price_gross' => 12300])
            ->refresh();
    }

    /** @test */
    public function product_percent_discount_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $discountA = tap(new Discount(), function (Discount $discount) {
            $percentDiscountA = ProductPercentDiscount::create([
                'name' => '60% OFF discount',
                'value' => 60.0,
            ]);

            $discount->discountable()->associate($this->productA);
            $discount->discount()->associate($percentDiscountA);
        });

        $discountA->save();

        $discountB = tap(new Discount(), function (Discount $discount) {
            $percentDiscountB = ProductPercentDiscount::create([
                'name' => '13% OFF discount',
                'value' => 13.0,
            ]);

            $discount->discountable()->associate($this->productB);
            $discount->discount()->associate($percentDiscountB);
        });

        $discountB->save();

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2000,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => [
                        'name' => '60% OFF discount',
                        'value' => 60,
                        'unit' => '%',
                    ],
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 6525,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => [
                        'name' => '13% OFF discount',
                        'value' => 13,
                        'unit' => '%',
                    ],
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 12300,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson(['cartItems' => $expected]);
    }

    /** @test */
    public function product_value_discount_is_working()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $discountA = tap(new Discount(), function (Discount $discount) {
            $percentDiscountA = ProductValueDiscount::create([
                'name' => '1000 OFF discount',
                'value' => 1000.0,
            ]);

            $discount->discountable()->associate($this->productA);
            $discount->discount()->associate($percentDiscountA);
        });

        $discountA->save();

        $discountB = tap(new Discount(), function (Discount $discount) {
            $percentDiscountB = ProductValueDiscount::create([
                'name' => '4400 OFF discount',
                'value' => 4400.0,
            ]);

            $discount->discountable()->associate($this->productB);
            $discount->discount()->associate($percentDiscountB);
        });

        $discountB->save();

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 4000,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => [
                        'name' => '1000 OFF discount',
                        'value' => 1000,
                        'unit' => ':currency',
                    ],
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 3100,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => [
                        'name' => '4400 OFF discount',
                        'value' => 4400.0,
                        'unit' => ':currency',
                    ],
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 12300,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => null,
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson(['cartItems' => $expected]);
    }
}
