<?php

namespace DV5150\Shop\Observers;

use DV5150\Shop\Contracts\Deals\DiscountContract;

class DeleteDiscountObserver
{
    public function deleted(DiscountContract $productDiscount)
    {
        $productDiscount->discounts->each->delete();
    }
}
