<?php

namespace DV5150\Shop\Contracts\Deals;

use DV5150\Shop\Models\CartItemCapsule;
use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface DiscountContract extends BaseDealContract
{
    public function baseDiscount(): MorphOne;
    public function getBaseDiscount(): Discount;
    public function getDiscountedPriceGross(CartItemCapsule $capsule): float;
}
