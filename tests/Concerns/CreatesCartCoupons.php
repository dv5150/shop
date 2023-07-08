<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Models\Deals\Coupons\CartPercentCoupon;
use DV5150\Shop\Models\Deals\Coupons\CartValueCoupon;

trait CreatesCartCoupons
{
    protected function createCartPercentCoupon(string $name, float $value, string $code): BaseCouponContract
    {
        return $this->createCoupon(type: CartPercentCoupon::class, name: $name, value: $value, code: $code);
    }

    protected function createCartValueCoupon(string $name, float $value, string $code): BaseCouponContract
    {
        return $this->createCoupon(type: CartValueCoupon::class, name: $name, value: $value, code: $code);
    }

    protected function createCoupon(string $type, string $name, float $value, string $code): BaseCouponContract
    {
        $coupon = tap(
            new (config('shop.models.coupon'))(['code' => $code]),
            function (BaseCouponContract $coupon) use ($type, $name, $value) {
                $concreteCoupon = $type::create([
                    'name' => $name,
                    'value' => $value,
                ]);

                $coupon->coupon()->associate($concreteCoupon);
            }
        );

        $coupon->save();

        return $coupon;
    }
}
