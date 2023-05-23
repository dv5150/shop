<?php

namespace DV5150\Shop\Contracts\Deals;

use DV5150\Shop\Support\CartCollection;

interface CouponContract extends BaseDealContract
{
    public function getDiscountedPriceGross(CartCollection $cart): float;
}
