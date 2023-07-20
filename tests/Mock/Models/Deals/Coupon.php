<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals;

use DV5150\Shop\Models\Deals\Coupon as ShopCoupon;
use DV5150\Shop\Tests\Mock\Factories\CouponFactory;

class Coupon extends ShopCoupon
{
    protected static function newFactory()
    {
        return CouponFactory::new();
    }
}