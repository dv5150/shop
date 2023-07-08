<?php

namespace DV5150\Shop\Concerns\Models\User;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrders
{
    public function orders(): HasMany
    {
        return $this->hasMany(config('shop.models.order'));
    }
}
