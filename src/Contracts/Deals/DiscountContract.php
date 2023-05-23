<?php

namespace DV5150\Shop\Contracts\Deals;

use DV5150\Shop\Models\CartItemCapsule;

interface DiscountContract extends BaseDealContract
{
    public function getDiscountedPriceGross(CartItemCapsule $capsule): float;
}
