<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;

interface CouponServiceContract
{
    public function setCoupon(?BaseCouponContract $coupon): void;
    public function getCoupon(): ?BaseCouponContract;
}
