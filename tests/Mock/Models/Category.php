<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Models\Default\Category as ShopCategory;
use DV5150\Shop\Tests\Mock\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class Category extends ShopCategory
{
    public static function newFactory(): Factory
    {
        return CategoryFactory::new();
    }
}
