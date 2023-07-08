<?php

namespace DV5150\Shop\Concerns\Models\SellableItem;

use DV5150\Shop\Contracts\Models\SellableItemContract;

trait DetachesAllDiscounts
{
    public static function bootDetachesAllDiscounts(): void
    {
        static::deleting(function (SellableItemContract $sellableItem) {
            $sellableItem->discounts()->sync([]);
        });
    }
}