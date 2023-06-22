<?php

namespace DV5150\Shop\Concerns\Deals;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;

trait DeletesConcreteDiscount
{
    public static function bootDeletesConcreteDiscount()
    {
        static::deleting(function (BaseDiscountContract $baseDiscount) {
            $baseDiscount->discount()->delete();
        });
    }
}