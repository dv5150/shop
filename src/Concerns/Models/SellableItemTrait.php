<?php

namespace DV5150\Shop\Concerns\Models;

use DV5150\Shop\Concerns\Models\SellableItem\DetachesAllDiscounts;
use DV5150\Shop\Concerns\Models\SellableItem\DetachesFromOrderItems;

trait SellableItemTrait
{
    use DetachesAllDiscounts,
        DetachesFromOrderItems;
}