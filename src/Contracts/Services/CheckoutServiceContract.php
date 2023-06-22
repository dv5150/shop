<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Models\OrderContract;

interface CheckoutServiceContract
{
    public function saveOrder(array $orderData): OrderContract;
    public function saveItems(OrderContract $order, array $cartData): void;
}