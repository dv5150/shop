<?php

namespace DV5150\Shop\Contracts\Controllers;

use DV5150\Shop\Http\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

interface CheckoutAPIControllerContract
{
    public function store(StoreOrderRequest $request): JsonResponse;
}