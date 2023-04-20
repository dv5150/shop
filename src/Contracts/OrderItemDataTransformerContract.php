<?php

namespace DV5150\Shop\Contracts;

interface OrderItemDataTransformerContract
{
    public function transform(ProductContract $product, int $quantity): array;
}
