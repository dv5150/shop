<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;

class OrderItemDataTransformer implements OrderItemDataTransformerContract
{
    public function transform(CartItemCapsuleContract $capsule): array
    {
        return array_merge(['quantity' => $capsule->getQuantity()], [
            'name' => $capsule->getProduct()->getName(),
            'price_gross' => $capsule->getPriceGross(),
            'info' => $capsule->getDiscount()?->getShortName(),
        ]);
    }
}
