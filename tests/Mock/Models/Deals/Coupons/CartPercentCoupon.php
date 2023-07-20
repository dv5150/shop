<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals\Coupons;

use DV5150\Shop\Models\Deals\Coupons\CartPercentCoupon as ShopCartPercentCoupon;
use DV5150\Shop\Tests\Mock\Factories\Coupons\CartPercentCouponFactory;

class CartPercentCoupon extends ShopCartPercentCoupon
{
    protected static function newFactory()
    {
        return CartPercentCouponFactory::new();
    }
}