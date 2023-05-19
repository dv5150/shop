<?php

namespace DV5150\Shop\Tests\Unit;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\TestCase;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\Discount;
use DV5150\Shop\Models\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Discounts\ProductValueDiscount;
use DV5150\Shop\Tests\Mock\Models\Product;

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

    /** @test */
    public function best_available_price_is_applied_when_removing_a_discount()
    {
        Cart::addItem($this->productA);
        Cart::addItem($this->productB);

        $discountAA = $this->createPercentDiscountForProduct($this->productA, '55% OFF discount', 55.0);
        $discountAB = $this->createPercentDiscountForProduct($this->productA, '88% OFF discount', 88.0);

        $discountBA = $this->createValueDiscountForProduct($this->productB, '510 OFF discount', 510.0);
        $discountBB = $this->createValueDiscountForProduct($this->productB, '1510 OFF discount', 1510.0);

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 600,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAB->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 5990,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBB->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson(['cartItems' => $expected]);

        $discountAB->delete();
        $discountBB->delete();

        $expected = [
            [
                'item' => [
                    'id' => $this->productA->getID(),
                    'name' => $this->productA->getName(),
                    'price_gross' => 2250,
                    'price_gross_original' => $this->productA->getPriceGross(),
                    'discount' => $discountAA->toArray(),
                ],
                'quantity' => 1,
            ],
            [
                'item' => [
                    'id' => $this->productB->getID(),
                    'name' => $this->productB->getName(),
                    'price_gross' => 6990,
                    'price_gross_original' => $this->productB->getPriceGross(),
                    'discount' => $discountBA->toArray(),
                ],
                'quantity' => 1,
            ],
        ];

        $this->get(route('api.shop.cart.index'))
            ->assertJson(['cartItems' => $expected]);

        $discountAA->delete();
        $discountBA->delete();

        $expected = [
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
            ->assertJson(['cartItems' => $expected]);
    }

    protected function createPercentDiscountForProduct(Product $product, string $name, float $value): Discount
    {
        $discount = tap(new Discount(), function (Discount $discount) use ($name, $value, $product) {
            $percentDiscount = ProductPercentDiscount::create([
                'name' => $name,
                'value' => $value,
            ]);

            $discount->discountable()->associate($product);
            $discount->discount()->associate($percentDiscount);
        });

        $discount->save();

        return $discount;
    }

    protected function createValueDiscountForProduct(Product $product, string $name, float $value): Discount
    {
        $discount = tap(new Discount(), function (Discount $discount) use ($name, $value, $product) {
            $percentDiscount = ProductValueDiscount::create([
                'name' => $name,
                'value' => $value,
            ]);

            $discount->discountable()->associate($product);
            $discount->discount()->associate($percentDiscount);
        });

        $discount->save();

        return $discount;
    }
}
