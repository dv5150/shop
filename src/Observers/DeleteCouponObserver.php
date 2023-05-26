<?php

namespace DV5150\Shop\Observers;

use DV5150\Shop\Contracts\Deals\CouponContract;

class DeleteCouponObserver
{
    public function deleted(CouponContract $cartCoupon)
    {
        $cartCoupon->coupons->each->delete();
    }
}
