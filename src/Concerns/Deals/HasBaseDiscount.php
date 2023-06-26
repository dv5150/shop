<?php

namespace DV5150\Shop\Concerns\Deals;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBaseDiscount
{
    public function baseDiscount(): MorphOne
    {
        return $this->morphOne(config('shop.models.discount'), 'discount');
    }

    public function getBaseDiscount(): BaseDiscountContract
    {
        return $this->baseDiscount;
    }
}
