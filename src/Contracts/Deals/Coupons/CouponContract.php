<?php

namespace DV5150\Shop\Contracts\Deals\Coupons;

use DV5150\Shop\Contracts\Models\OrderItemContract;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

interface CouponContract extends BaseCouponContract
{
    public function baseCoupon(): MorphOne;
    public function getBaseCoupon(): BaseCouponContract;
    public function toOrderItem(Collection $orderItems): OrderItemContract;
}
