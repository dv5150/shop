<?php

namespace DV5150\Shop\Concerns;

use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBaseDiscount
{
    public function baseDiscount(): MorphOne
    {
        return $this->morphOne(Discount::class, 'discount');
    }
}
