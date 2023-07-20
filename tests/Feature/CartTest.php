<?php

namespace DV5150\Shop\Tests\Feature;

use DV5150\Shop\Facades\Cart;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('can add items to the cart', function (): void {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    post(route('api.shop.cart.store', ['product' => $productA]))->assertOk();
    post(route('api.shop.cart.store', ['product' => $productB]))->assertOk();

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(2)
        ->toBe([
            (new ($this->shopItemCapsuleClass)($productA))->toArray(),
            (new ($this->shopItemCapsuleClass)($productB))->toArray(),
        ]);

    post(route('api.shop.cart.store', [
        'product' => $productA,
        'quantity' => 5,
    ]))->assertOk();

    post(route('api.shop.cart.store', [
        'product' => $productB,
        'quantity' => 9,
    ]))->assertOk();

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(2)
        ->toBe([
            (new ($this->shopItemCapsuleClass)($productA, 6))->toArray(),
            (new ($this->shopItemCapsuleClass)($productB, 10))->toArray(),
        ]);
});

it('can remove items from the cart', function (): void {
    list($productA, $productB, $productC) = $this->productClass::factory()->count(3)->create()->all();

    post(route('api.shop.cart.store', [
        'product' => $productA,
        'quantity' => 15,
    ]));

    post(route('api.shop.cart.store', [
        'product' => $productB,
        'quantity' => 12,
    ]));

    post(route('api.shop.cart.store', [
        'product' => $productC,
        'quantity' => 6,
    ]));

    post(route('api.shop.cart.remove', ['product' => $productA]));
    post(route('api.shop.cart.remove', ['product' => $productB]));
    post(route('api.shop.cart.remove', ['product' => $productC]));

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(3)
        ->toBe([
            (new ($this->shopItemCapsuleClass)($productA, 14))->toArray(),
            (new ($this->shopItemCapsuleClass)($productB, 11))->toArray(),
            (new ($this->shopItemCapsuleClass)($productC, 5))->toArray(),
        ]);

    post(route('api.shop.cart.remove', [
        'product' => $productA,
        'quantity' => 14,
    ]));

    post(route('api.shop.cart.remove', [
        'product' => $productB,
        'quantity' => 11,
    ]));

    post(route('api.shop.cart.remove', [
        'product' => $productC,
        'quantity' => 999,
    ]));

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(0);
});

it('can erase items from cart', function () {
    $productA = $this->productClass::factory()->create();

    post(route('api.shop.cart.store', [
        'product' => $productA,
        'quantity' => 15,
    ]));

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(1)
        ->toBe([
            (new ($this->shopItemCapsuleClass)($productA, 15))->toArray(),
        ]);

    delete(route('api.shop.cart.erase', ['product' => $productA]));

    expect($response = get(route('api.shop.cart.index')))
        ->assertOk()
        ->and($response->getContent())
        ->json()
        ->cart
        ->items
        ->toHaveCount(0);
});

it('can be recognized as a digital cart', function () {
    list($productA, $productB) = $this->productClass::factory()->count(2)->create()->all();

    $productC = $this->productClass::factory()->digital()->create();

    post(route('api.shop.cart.store', [
        'product' => $productA,
        'quantity' => 3,
    ]));

    post(route('api.shop.cart.store', [
        'product' => $productB,
        'quantity' => 6,
    ]));

    post(route('api.shop.cart.store', [
        'product' => $productC,
        'quantity' => 2,
    ]));

    expect(Cart::hasDigitalItemsOnly())->toBeFalse();

    delete(route('api.shop.cart.erase', ['product' => $productA]));
    delete(route('api.shop.cart.erase', ['product' => $productB]));

    expect(Cart::hasDigitalItemsOnly())->toBeTrue();
});
