<?php

namespace DV5150\Shop\Tests\Mock\Models\Deals\Coupons;

use DV5150\Shop\Models\Deals\Coupons\CartValueCoupon as ShopCartValueCoupon;
use DV5150\Shop\Tests\Mock\Factories\Coupons\CartValueCouponFactory;

class CartValueCoupon extends ShopCartValueCoupon
{
    protected static function newFactory()
    {
        return CartValueCouponFactory::new();
    }
}