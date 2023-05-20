<?php

namespace DV5150\Shop\Contracts;

use DV5150\Shop\Models\CartItemCapsule;

interface OrderItemDataTransformerContract
{
    public function transform(CartItemCapsule $capsule): array;
}
