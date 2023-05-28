<?php

namespace DV5150\Shop\Concerns\User;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAddresses
{
    public function billingAddresses(): HasMany
    {
        return $this->hasMany(config('shop.models.billingAddress'));
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(config('shop.models.shippingAddress'));
    }
}
