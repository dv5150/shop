<?php

namespace DV5150\Shop\Concerns\Models\Product;

use DV5150\Shop\Contracts\Models\SellableItemContract;

trait DetachesAllDiscounts
{
    public static function bootDetachesAllDiscounts()
    {
        static::deleting(function (SellableItemContract $sellableItem) {
            $sellableItem->discounts()->sync([]);
        });
    }
}