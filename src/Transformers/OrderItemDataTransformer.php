<?php

namespace DV5150\Shop\Transformers;

use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\ProductContract;

class OrderItemDataTransformer implements OrderItemDataTransformerContract
{
    public function transform(ProductContract $product, int $quantity): array
    {
        return array_merge(['quantity' => $quantity], [
            'name' => $product->getName(),
            'price_gross' => $product->getPriceGross(),
        ]);
    }
}
