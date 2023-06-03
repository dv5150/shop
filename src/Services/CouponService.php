<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Models\Deals\Coupon;
use Illuminate\Support\Facades\Session;

class CouponService implements CouponServiceContract
{
    protected const SESSION_KEY = 'coupon';

    public function getCoupon(): ?Coupon
    {
        if ($coupon = Session::get(self::SESSION_KEY)) {
            $coupon = !is_null($coupon) ? unserialize($coupon) : null;
            $coupon = $coupon?->exists() ? $coupon : null;
        }

        $this->setCoupon($coupon);

        return $coupon;
    }

    public function setCoupon(?Coupon $coupon): void
    {
        Session::put(
            self::SESSION_KEY,
            $coupon?->exists() ? serialize($coupon) : null
        );
    }
}
