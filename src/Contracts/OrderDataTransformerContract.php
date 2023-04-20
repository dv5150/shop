<?php

namespace DV5150\Shop\Contracts;

interface OrderDataTransformerContract
{
    public function transform(array $data): array;
}
