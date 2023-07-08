<?php

namespace DV5150\Shop\Concerns\Deals\Coupon;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;

trait DeletesConcreteCoupon
{
    public static function bootDeletesConcreteCoupon(): void
    {
        static::deleting(function (BaseCouponContract $baseCoupon) {
            $baseCoupon->coupon()->delete();
        });
    }
}