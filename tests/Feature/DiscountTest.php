<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Tests\Mock\Models\Deals\Discount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductValueDiscount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('calculates the product discounts properly', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 5000.0],
            ['price_gross' => 7500.0],
            ['price_gross' => 12300.0],
        ))
        ->create()
        ->all();

    Cart::addItem($productA);
    Cart::addItem($productB);
    Cart::addItem($productC);

    $discountA = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '60% OFF discount',
            'value' => 60.0,
        ])->getBaseDiscount();

    $discountB = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '700 OFF discount',
            'value' => 700.0,
        ])->getBaseDiscount();

    $productA->discounts()->sync($discountA);
    $productB->discounts()->sync($discountB);

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(3)
        ->toBe([
            [
                'item' => [
                    'id' => $productA->getKey(),
                    'name' => $productA->getName(),
                    'price_gross' => 2000.0,
                    'price_gross_original' => 5000.0,
                    'discount' => $discountA->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 2000.0,
            ],
            [
                'item' => [
                    'id' => $productB->getKey(),
                    'name' => $productB->getName(),
                    'price_gross' => 6800.0,
                    'price_gross_original' => 7500.0,
                    'discount' => $discountB->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 6800.0,
            ],
            [
                'item' => [
                    'id' => $productC->getKey(),
                    'name' => $productC->getName(),
                    'price_gross' => 12300.0,
                    'price_gross_original' => 12300.0,
                    'discount' => null,
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 12300.0,
            ]
        ]);
});

it('applies the best available discount on products', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 5000.0],
            ['price_gross' => 7500.0],
            ['price_gross' => 12300.0],
        ))
        ->create()
        ->all();

    Cart::addItem($productA);
    Cart::addItem($productB);
    Cart::addItem($productC);

    $discountAA = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '1000 OFF discount',
            'value' => 1000.0,
        ])->getBaseDiscount();

    $discountAB = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '3000 OFF discount',
            'value' => 3000.0,
        ])->getBaseDiscount();

    $productA->discounts()->sync(new Collection([$discountAA, $discountAB]));

    $discountBA = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '70% OFF discount',
            'value' => 70.0,
        ])->getBaseDiscount();

    $discountBB = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '20% OFF discount',
            'value' => 20.0,
        ])->getBaseDiscount();

    $productB->discounts()->sync(new Collection([$discountBA, $discountBB]));

    $discountCA = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '3% OFF discount',
            'value' => 3.0,
        ])->getBaseDiscount();

    $discountCB = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '1000 OFF discount',
            'value' => 1000.0,
        ])->getBaseDiscount();

    $productC->discounts()->sync(new Collection([$discountCA, $discountCB]));

    expect(get(route('api.shop.cart.index'))->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(3)
        ->toBe([
            [
                'item' => [
                    'id' => $productA->getKey(),
                    'name' => $productA->getName(),
                    'price_gross' => 2000.0,
                    'price_gross_original' => 5000.0,
                    'discount' => $discountAB->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 2000.0,
            ],
            [
                'item' => [
                    'id' => $productB->getKey(),
                    'name' => $productB->getName(),
                    'price_gross' => 2250.0,
                    'price_gross_original' => 7500.0,
                    'discount' => $discountBA->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 2250.0,
            ],
            [
                'item' => [
                    'id' => $productC->getKey(),
                    'name' => $productC->getName(),
                    'price_gross' => 11300.0,
                    'price_gross_original' => 12300.0,
                    'discount' => $discountCB->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 11300.0,
            ]
        ]);

    // Try removing a discount

    $discountCB->delete();

    expect(get(route('api.shop.cart.index'))->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(3)
        ->toBe([
            [
                'item' => [
                    'id' => $productA->getKey(),
                    'name' => $productA->getName(),
                    'price_gross' => 2000.0,
                    'price_gross_original' => 5000.0,
                    'discount' => $discountAB->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 2000.0,
            ],
            [
                'item' => [
                    'id' => $productB->getKey(),
                    'name' => $productB->getName(),
                    'price_gross' => 2250.0,
                    'price_gross_original' => 7500.0,
                    'discount' => $discountBA->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 2250.0,
            ],
            [
                'item' => [
                    'id' => $productC->getKey(),
                    'name' => $productC->getName(),
                    'price_gross' => 11931.0,
                    'price_gross_original' => 12300.0,
                    'discount' => $discountCA->toArray(),
                    'is_digital' => false,
                ],
                'quantity' => 1,
                'subtotal' => 11931.0,
            ]
        ]);
});

it('saves order items with discounts properly', function () {
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     * @var SellableItemContract $productC
     */
    list($productA, $productB, $productC) = $this->productClass::factory()
        ->count(3)
        ->state(new Sequence(
            ['price_gross' => 28990.0],
            ['price_gross' => 9990.0],
            ['price_gross' => 14370.0],
        ))
        ->create()
        ->all();

    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());

    Cart::addItem($productA, 2);
    Cart::addItem($productB, 2);
    Cart::addItem($productC, 2);

    $discountA = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '1700 OFF discount',
            'value' => 1700.0,
        ])->getBaseDiscount();

    $discountB = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '11% OFF discount',
            'value' => 11.0,
        ])->getBaseDiscount();

    $productA->discounts()->sync($discountA);
    $productB->discounts()->sync($discountB);

    post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 2,
            ],
            [
                'item' => ['id' => $productC->getKey()],
                'quantity' => 2,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $order = config('shop.models.order')::first();

    $this->assertDatabaseHas('orders', array_merge($this->expectedOrderDataRequired, [
        'user_id' => null,
    ]));

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'name' => $productA->getName(),
        'quantity' => 2,
        'price_gross' => 27290.0,
        'type' => 'product',
        'sellable_type' => $productA::class,
        'sellable_id' => $productA->getKey(),
        'info' => $discountA->getName(),
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'name' => $productB->getName(),
        'quantity' => 2,
        'price_gross' => 8891.0,
        'type' => 'product',
        'sellable_type' => $productB::class,
        'sellable_id' => $productB->getKey(),
        'info' => $discountB->getName(),
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->getKey(),
        'name' => $productC->getName(),
        'quantity' => 2,
        'price_gross' => 14370.0,
        'type' => 'product',
        'sellable_type' => $productC::class,
        'sellable_id' => $productC->getKey(),
        'info' => null,
    ]);
});

it('deletes discounts when base discount is being deleted', function () {
    $baseDiscountA = ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '1700 OFF discount',
            'value' => 1700.0,
        ])->getBaseDiscount();

    $baseDiscountB = ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '11% OFF discount',
            'value' => 11.0,
        ])->getBaseDiscount();

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
});
