<?php

namespace DV5150\Shop\Observers;

use DV5150\Shop\Models\Deals\Coupon;

class DeleteCouponObserver
{
    public function deleting(Coupon $baseCoupon)
    {
        $baseCoupon->coupon->delete();
    }
}
