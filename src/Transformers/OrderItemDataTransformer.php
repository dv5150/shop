<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract;
use Illuminate\Support\Str;

class OrderItemDataTransformer implements OrderItemDataTransformerContract
{
    public function transform(ShopItemCapsuleContract $capsule): array
    {
        return array_merge(['quantity' => $capsule->getQuantity()], [
            'name' => $capsule->getSellableItem()->getName(),
            'price_gross' => $capsule->getPriceGross(),
            'info' => $capsule->getDiscount()?->getName(),
            'type' => Str::kebab(class_basename($capsule->getSellableItem())),
        ]);
    }
}
