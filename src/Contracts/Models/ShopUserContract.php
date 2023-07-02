<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface ShopUserContract
{
    public function shippingAddresses(): HasMany;
    public function orders(): HasMany;
    public function getShippingAddresses(): AnonymousResourceCollection;
}