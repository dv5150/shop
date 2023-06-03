<?php

namespace DV5150\Shop\Contracts;

interface OrderDataTransformerContract
{
    public function rules(): array;
    public function transform(array $orderData): array;
}
