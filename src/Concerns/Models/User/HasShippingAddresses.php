<?php

namespace DV5150\Shop\Concerns\Models\User;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait HasShippingAddresses
{
    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(config('shop.models.shippingAddress'));
    }

    public function getShippingAddresses(): AnonymousResourceCollection
    {
        return config('shop.resources.shippingAddress')::collection(
            $this->shippingAddresses()->get()
        );
    }
}
