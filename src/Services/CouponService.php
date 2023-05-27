<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Models\Deals\Coupon;
use Illuminate\Support\Facades\Session;

class CouponService implements CouponServiceContract
{
    public function getCoupon(): ?Coupon
    {
        if ($coupon = Session::get($this->getSessionKey())) {
            $coupon = !is_null($coupon) ? unserialize($coupon) : null;
            $coupon = $coupon?->exists() ? $coupon : null;
        }

        $this->setCoupon($coupon);

        return $coupon;
    }

    public function setCoupon(?Coupon $coupon): void
    {
        Session::put(
            $this->getSessionKey(),
            $coupon?->exists() ? serialize($coupon) : null
        );
    }

    protected function getSessionKey(): string
    {
        return 'coupon';
    }
}
