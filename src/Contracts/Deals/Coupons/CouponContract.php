<?php

namespace DV5150\Shop\Contracts\Deals\Coupons;

use DV5150\Shop\Contracts\Models\OrderItemContract;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

interface CouponContract extends BaseCouponContract
{
    public function baseCoupon(): MorphOne;
    public function getBaseCoupon(): BaseCouponContract;
    public function getCode(): string;
    public function toOrderItem(Collection $orderItems): OrderItemContract;

    public function getName(): string|null;
    public function getValue(): float;
    public function getUnit(): string;
}
