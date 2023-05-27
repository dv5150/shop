<?php

namespace DV5150\Shop\Concerns;

use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBaseDiscounts
{
    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discount');
    }
}
