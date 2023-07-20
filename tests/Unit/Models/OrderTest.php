<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\post;

test('an order has multiple order items', function () {
    /** @var ShippingModeContract $shippingMode */
    $shippingMode = config('shop.models.shippingMode')::factory()
        ->create();

    $shippingMode
        ->paymentModes()
        ->sync(config('shop.models.paymentMode')::factory()->create());
    /**
     * @var SellableItemContract $productA
     * @var SellableItemContract $productB
     */
    list($productA, $productB) = $this->productClass::factory()
        ->count(2)
        ->state(new Sequence(
            ['price_gross' => 3000.0],
            ['price_gross' => 8000.0],
        ))
        ->create()
        ->all();

    post(route('api.shop.checkout.store'), array_merge($this->testOrderDataRequired, [
        'cartData' => [
            [
                'item' => ['id' => $productA->getKey()],
                'quantity' => 7,
            ],
            [
                'item' => ['id' => $productB->getKey()],
                'quantity' => 7,
            ],
        ],
        'shippingMode' => [
            'provider' => $shippingMode->getProvider(),
        ],
        'paymentMode' => [
            'provider' => $shippingMode->paymentModes()->first()->getProvider(),
        ],
    ]));

    $order = config('shop.models.order')::with('items')->first();

    expect($order)
        ->toBeInstanceOf(OrderContract::class)
        ->and($order->items)
        ->toBeInstanceOf(Collection::class)
        ->and($order->items)
        ->each()
        ->toBeInstanceOf(OrderItemContract::class);

    $order->items->each(function (OrderItemContract $orderItem) {
        expect($orderItem->getSellable())->toBeInstanceOf(SellableItemContract::class);
    });
});