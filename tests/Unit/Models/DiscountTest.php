<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Tests\Mock\Models\Deals\Discount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Tests\Mock\Models\Deals\Discounts\ProductValueDiscount;

test('discounts have a base discount', function () {
    ProductPercentDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '10% OFF discount',
            'value' => 10.0,
        ])->getBaseDiscount();

    ProductValueDiscount::factory()
        ->afterCreating(function (DiscountContract $discount) {
            /** @var BaseDiscountContract $baseDiscount */
            $baseDiscount = Discount::factory()->make();
            $baseDiscount->discount()->associate($discount);
            $baseDiscount->save();
        })
        ->create([
            'name' => '100 OFF discount',
            'value' => 100.0,
        ])->getBaseDiscount();

    expect(ProductPercentDiscount::first()->getBaseDiscount())
        ->toBeInstanceOf(config('shop.models.discount'))
        ->and(ProductValueDiscount::first()->getBaseDiscount())
        ->toBeInstanceOf(config('shop.models.discount'));
});