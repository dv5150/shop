<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Models\Deals\Discount;
use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount;

trait CreatesDiscountsForProducts
{
    protected function createPercentDiscountForProduct(ProductContract $product, string $name, float $value): Discount
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

    protected function createValueDiscountForProduct(ProductContract $product, string $name, float $value): Discount
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
