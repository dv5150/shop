<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Models\Deals\Discounts\ProductPercentDiscount;
use DV5150\Shop\Models\Deals\Discounts\ProductValueDiscount;
use DV5150\Shop\Tests\Concerns\CreatesDiscountsForProducts;
use DV5150\Shop\Tests\TestCase;

class DiscountTest extends TestCase
{
    use CreatesDiscountsForProducts;

    /** @test */
    public function discounts_have_a_base_discount()
    {
        $this->createPercentDiscountForProduct(
            $this->productA,
            '10% OFF discount',
            10.0
        );

        $this->createValueDiscountForProduct(
            $this->productB,
            '100 OFF discount',
            100.0
        );

        $this->assertInstanceOf(config('shop.models.discount'), ProductPercentDiscount::first()->getBaseDiscount());
        $this->assertInstanceOf(config('shop.models.discount'), ProductValueDiscount::first()->getBaseDiscount());
    }
}