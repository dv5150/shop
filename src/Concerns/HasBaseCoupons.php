<?php

namespace DV5150\Shop\Concerns;

use DV5150\Shop\Models\Coupon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBaseCoupons
{
    public function coupons(): MorphMany
    {
        return $this->morphMany(Coupon::class, 'coupon');
    }
}
