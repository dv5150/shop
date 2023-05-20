<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Models\CartItemCapsule;

class OrderItemDataTransformer implements OrderItemDataTransformerContract
{
    public function transform(CartItemCapsule $capsule): array
    {
        return array_merge(['quantity' => $capsule->getQuantity()], [
            'name' => $capsule->getItem()->getName(),
            'price_gross' => $capsule->getPriceGross(),
            'info' => $capsule->getDiscount()?->getFullName(),
        ]);
    }
}
