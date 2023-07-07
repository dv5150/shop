<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount;

trait CreatesDiscountsForProducts
{
    protected function createPercentDiscountForProduct(
        SellableItemContract $sellableItem,
        string $name,
        float $value
    ): BaseDiscountContract
    {
        $discount = tap(
            new (config('shop.models.discount'))(),
            function (BaseDiscountContract $discount) use ($name, $value) {
                $percentDiscount = ProductPercentDiscount::create([
                    'name' => $name,
                    'value' => $value,
                ]);

                $discount->discount()->associate($percentDiscount);
            });

        $discount->save();

        $sellableItem->discounts()->attach($discount);

        return $discount;
    }

    protected function createValueDiscountForProduct(
        SellableItemContract $sellableItem,
        string $name,
        float $value
    ): BaseDiscountContract
    {
        $discount = tap(
            new (config('shop.models.discount'))(),
            function (BaseDiscountContract $discount) use ($name, $value) {
                $percentDiscount = ProductValueDiscount::create([
                    'name' => $name,
                    'value' => $value,
                ]);

                $discount->discount()->associate($percentDiscount);
            });

        $discount->save();

        $sellableItem->discounts()->attach($discount);

        return $discount;
    }
}
