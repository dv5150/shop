<?php

namespace DV5150\Shop\Concerns;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Models\Deals\Coupon;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBaseCoupon
{
    public function baseCoupon(): MorphOne
    {
        return $this->morphOne(Coupon::class, 'coupon');
    }

    public function getBaseCoupon(): BaseCouponContract
    {
        return $this->baseCoupon;
    }

    public function getCode(): string
    {
        return $this->getBaseCoupon()->getCode();
    }
}
