<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount;

trait CreatesDiscountsForProducts
{
    protected function createPercentDiscountForProduct(
        ProductContract $product, string $name, float $value
    ): BaseDiscountContract
    {
        $discount = tap(
            new (config('shop.models.discount'))(),
            function (BaseDiscountContract $discount) use ($name, $value, $product) {
                $percentDiscount = ProductPercentDiscount::create([
                    'name' => $name,
                    'value' => $value,
                ]);

                $discount->discount()->associate($percentDiscount);
            });

        $discount->save();

        $product->discounts()->attach($discount);

        return $discount;
    }

    protected function createValueDiscountForProduct(
        ProductContract $product, string $name, float $value
    ): BaseDiscountContract
    {
        $discount = tap(
            new (config('shop.models.discount'))(),
            function (BaseDiscountContract $discount) use ($name, $value, $product) {
                $percentDiscount = ProductValueDiscount::create([
                    'name' => $name,
                    'value' => $value,
                ]);

                $discount->discount()->associate($percentDiscount);
            });

        $discount->save();

        $product->discounts()->attach($discount);

        return $discount;
    }
}
