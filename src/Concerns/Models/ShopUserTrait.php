<?php

namespace DV5150\Shop\Concerns\Models;

use DV5150\Shop\Concerns\Models\User\HasOrders;
use DV5150\Shop\Concerns\Models\User\HasShippingAddresses;

trait ShopUserTrait
{
    use HasShippingAddresses,
        HasOrders;
}