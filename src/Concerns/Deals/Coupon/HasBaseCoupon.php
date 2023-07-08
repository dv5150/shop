<?php

namespace DV5150\Shop\Concerns\Deals\Coupon;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBaseCoupon
{
    public function baseCoupon(): MorphOne
    {
        return $this->morphOne(config('shop.models.coupon'), 'coupon');
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
