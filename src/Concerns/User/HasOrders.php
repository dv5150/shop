<?php

namespace DV5150\Shop\Concerns\User;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrders
{
    public function orders(): HasMany
    {
        return $this->hasMany(config('shop.models.order'));
    }
}
