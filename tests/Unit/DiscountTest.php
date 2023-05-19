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
                    'discount' => $discountA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 6525,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountB->toArray(),
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
            $valueDiscountA = ProductValueDiscount::create([
                'name' => '1000 OFF discount',
                'value' => 1000.0,
            ]);

            $discount->discountable()->associate($this->productA);
            $discount->discount()->associate($valueDiscountA);
        });

        $discountA->save();

        $discountB = tap(new Discount(), function (Discount $discount) {
            $valueDiscountB = ProductValueDiscount::create([
                'name' => '4400 OFF discount',
                'value' => 4400.0,
            ]);

            $discount->discountable()->associate($this->productB);
            $discount->discount()->associate($valueDiscountB);
        });

        $discountB->save();

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 4000,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 3100,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountB->toArray(),
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
    public function product_best_available_discount_is_applied()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);
        Cart::addItem($this->productC);

        $discountAA = tap(new Discount(), function (Discount $discount) {
            $valueDiscountAA = ProductValueDiscount::create([
                'name' => '1000 OFF discount',
                'value' => 1000.0,
            ]);

            $discount->discountable()->associate($this->productA);
            $discount->discount()->associate($valueDiscountAA);
        });

        $discountAA->save();

        $discountAB = tap(new Discount(), function (Discount $discount) {
            $valueDiscountAB = ProductValueDiscount::create([
                'name' => '3000 OFF discount',
                'value' => 3000.0,
            ]);

            $discount->discountable()->associate($this->productA);
            $discount->discount()->associate($valueDiscountAB);
        });

        $discountAB->save();

        $discountBA = tap(new Discount(), function (Discount $discount) {
            $percentDiscountBA = ProductPercentDiscount::create([
                'name' => '20% OFF discount',
                'value' => 20.0,
            ]);

            $discount->discountable()->associate($this->productB);
            $discount->discount()->associate($percentDiscountBA);
        });

        $discountBA->save();

        $discountBB = tap(new Discount(), function (Discount $discount) {
            $percentDiscountBB = ProductPercentDiscount::create([
                'name' => '70% OFF discount',
                'value' => 70.0,
            ]);

            $discount->discountable()->associate($this->productB);
            $discount->discount()->associate($percentDiscountBB);
        });

        $discountBB->save();

        $discountCA = tap(new Discount(), function (Discount $discount) {
            $percentDiscountCA = ProductPercentDiscount::create([
                'name' => '3% OFF discount',
                'value' => 3.0,
            ]);

            $discount->discountable()->associate($this->productC);
            $discount->discount()->associate($percentDiscountCA);
        });

        $discountCA->save();

        $discountCB = tap(new Discount(), function (Discount $discount) {
            $valueDiscountCB = ProductValueDiscount::create([
                'name' => '1000 OFF discount',
                'value' => 1000.0,
            ]);

            $discount->discountable()->associate($this->productC);
            $discount->discount()->associate($valueDiscountCB);
        });

        $discountCB->save();

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2000,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 2250,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productC->getID(),
                    'name' => $this->productC->getName(),
                    'price_gross' => 11300,
                    'price_gross_original' => $this->productC->getPriceGross(),
                    'discount' => $discountCB->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson(['cartItems' => $expected]);
    }
}
