<?php

namespace DV5150\Shop\Contracts\Deals\Discounts;

use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface BaseDiscountContract
{
    public static function bootDeletesConcreteDiscount(): void;

    public function discount(): MorphTo;
    public function getDiscount(): DiscountContract;
    public function getDiscountedPriceGross(ShopItemCapsuleContract $capsule): float;
}
