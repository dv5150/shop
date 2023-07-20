<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals\Discounts;

use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount as ShopProductValueDiscount;
use DV5150\Shop\Tests\Mock\Factories\Discounts\ProductValueDiscountFactory;

class ProductValueDiscount extends ShopProductValueDiscount
{
    protected static function newFactory()
    {
        return ProductValueDiscountFactory::new();
    }
}