<?php

namespace DV5150\Shop\Contracts\Transformers;

use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;

interface OrderItemDataTransformerContract
{
    public function transform(CartItemCapsuleContract $capsule): array;
}
