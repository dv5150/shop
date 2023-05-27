<?php

namespace DV5150\Shop\Contracts\Deals;

use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface DiscountContract extends BaseDealContract
{
    public function getDiscountedPriceGross(CartItemCapsule $capsule): float;
    public function baseDiscount(): MorphOne;
    public function getTypeName(): string;
}
