<?php

namespace DV5150\Shop\Concerns\Models\Product;

use DV5150\Shop\Contracts\Models\ProductContract;

trait DetachesAllDiscounts
{
    public static function bootDetachesAllDiscounts()
    {
        static::deleting(function (ProductContract $product) {
            $product->discounts()->sync([]);
        });
    }
}