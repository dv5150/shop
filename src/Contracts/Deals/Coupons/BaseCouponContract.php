<?php

namespace DV5150\Shop\Contracts\Deals\Coupons;

use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface BaseCouponContract
{
    public function coupon(): MorphTo;
    public function getCoupon(): CouponContract;
    public function getDiscountedPriceGross(CartCollection $cart): float;
    public function getCode(): string;
}