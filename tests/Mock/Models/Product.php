<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Models\Default\Product as ShopProduct;
use DV5150\Shop\Tests\Mock\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class Product extends ShopProduct
{
    public static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
