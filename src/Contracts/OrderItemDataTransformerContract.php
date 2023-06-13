<?php

namespace DV5150\Shop\Contracts;

use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;

interface OrderItemDataTransformerContract
{
    public function transform(CartItemCapsuleContract $capsule): array;
}
