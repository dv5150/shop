<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Models\Coupon;
use DV5150\Shop\Models\Coupons\CartPercentCoupon;
use DV5150\Shop\Models\Coupons\CartValueCoupon;

trait CreatesCartCoupons
{
    protected function createCartPercentCoupon(string $name, float $value, string $code): Coupon
    {
        $coupon = tap(new Coupon(['code' => $code]), function (Coupon $coupon) use ($name, $value) {
            $percentCoupon = CartPercentCoupon::create([
                'name' => $name,
                'value' => $value,
            ]);

            $coupon->coupon()->associate($percentCoupon);
        });

        $coupon->save();

        return $coupon;
    }

    protected function createCartValueCoupon(string $name, float $value, string $code): Coupon
    {
        $coupon = tap(new Coupon(['code' => $code]), function (Coupon $coupon) use ($name, $value) {
            $valueCoupon = CartValueCoupon::create([
                'name' => $name,
                'value' => $value,
            ]);

            $coupon->coupon()->associate($valueCoupon);
        });

        $coupon->save();

        return $coupon;
    }
}
