<?php

namespace DV5150\Shop\Contracts\Deals;

use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

interface CouponContract extends BaseDealContract
{
    public function getDiscountedPriceGross(CartCollection $cart): float;
    public function baseCoupon(): MorphOne;
    public function toOrderItem(Collection $orderItems): OrderItemContract;
    public function getTypeName(): string;
}
