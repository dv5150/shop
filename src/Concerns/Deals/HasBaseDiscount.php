<?php

namespace DV5150\Shop\Concerns\Deals;

use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBaseDiscount
{
    public function baseDiscount(): MorphOne
    {
        return $this->morphOne(config('shop.models.discount'), 'discount');
    }

    public function getBaseDiscount(): Discount
    {
        return $this->baseDiscount;
    }
}
