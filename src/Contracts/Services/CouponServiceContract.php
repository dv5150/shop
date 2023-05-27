<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Models\Deals\Coupon;

interface CouponServiceContract
{
    public function setCoupon(?Coupon $coupon): void;
    public function getCoupon(): ?Coupon;
}
