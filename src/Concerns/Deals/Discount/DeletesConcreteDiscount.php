<?php

namespace DV5150\Shop\Concerns\Deals\Discount;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;

trait DeletesConcreteDiscount
{
    public static function bootDeletesConcreteDiscount(): void
    {
        static::deleting(function (BaseDiscountContract $baseDiscount) {
            $baseDiscount->discount()->delete();
        });
    }
}