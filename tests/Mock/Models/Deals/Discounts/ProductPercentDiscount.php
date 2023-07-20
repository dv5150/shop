<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals\Discounts;

use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount as ShopProductPercentDiscount;
use DV5150\Shop\Tests\Mock\Factories\Discounts\ProductPercentDiscountFactory;

class ProductPercentDiscount extends ShopProductPercentDiscount
{
    protected static function newFactory()
    {
        return ProductPercentDiscountFactory::new();
    }
}