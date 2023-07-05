<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use Illuminate\Support\Facades\Session;

class CouponService implements CouponServiceContract
{
    protected const SESSION_KEY = 'coupon';

    public function getCoupon(): ?BaseCouponContract
    {
        if ($coupon = Session::get(self::SESSION_KEY)) {
            $coupon = !is_null($coupon) ? unserialize($coupon) : null;
            $coupon = $coupon?->exists() ? $coupon->refresh() : null;
        }

        $this->setCoupon($coupon);

        return $coupon;
    }

    public function setCoupon(?BaseCouponContract $coupon): void
    {
        Session::put(
            self::SESSION_KEY,
            $coupon?->exists() ? serialize($coupon->refresh()) : null
        );
    }
}
