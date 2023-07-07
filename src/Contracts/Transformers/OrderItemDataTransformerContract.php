<?php

namespace DV5150\Shop\Contracts\Transformers;

use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;

interface OrderItemDataTransformerContract
{
    public function transform(ShopItemCapsuleContract $capsule): array;
}
