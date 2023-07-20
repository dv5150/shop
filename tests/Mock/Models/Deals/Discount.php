<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals;

use DV5150\Shop\Models\Deals\Discount as ShopDiscount;
use DV5150\Shop\Tests\Mock\Factories\DiscountFactory;

class Discount extends ShopDiscount
{
    protected static function newFactory()
    {
        return DiscountFactory::new();
    }
}