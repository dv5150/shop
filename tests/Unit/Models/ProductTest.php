<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Tests\Mock\Models\Category;
use Illuminate\Database\Eloquent\Collection;

test('a product has multiple categories', function () {
    list($productA, $productB) = $this->productClass::factory()
        ->count(2)
        ->create()
        ->all();

    list($categoryA, $categoryB) = $this->categoryClass::factory()
        ->count(2)
        ->create()
        ->all();

    $productA->categories()->sync((new Collection([$categoryA, $categoryB]))->modelKeys());
    $productB->categories()->sync((new Collection([$categoryA, $categoryB]))->modelKeys());

    expect($productA->categories)->toBeInstanceOf(Collection::class)
        ->and($productB->categories)->toBeInstanceOf(Collection::class)
        ->and($productA->categories)->each()->toBeInstanceOf(Category::class)
        ->and($productB->categories)->each()->toBeInstanceOf(Category::class)
        ->and($categoryA->products)->toBeInstanceOf(Collection::class)
        ->and($categoryB->products)->toBeInstanceOf(Collection::class)
        ->and($categoryA->products)->each()->toBeInstanceOf(SellableItemContract::class)
        ->and($categoryB->products)->each()->toBeInstanceOf(SellableItemContract::class);
});