<?php

namespace DV5150\Shop\Observers;

use DV5150\Shop\Models\Deals\Discount;

class DeleteDiscountObserver
{
    public function deleting(Discount $baseDiscount)
    {
        $baseDiscount->discount->delete();
    }
}
