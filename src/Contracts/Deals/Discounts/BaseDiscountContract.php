<?php

namespace DV5150\Shop\Contracts\Deals\Discounts;

use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface BaseDiscountContract
{
    public function discount(): MorphTo;
    public function getDiscount(): DiscountContract;
    public function getDiscountedPriceGross(CartItemCapsuleContract $capsule): float;
}
