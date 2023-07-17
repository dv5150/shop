<?php

namespace DV5150\Shop\Http\Controllers\API;

use DV5150\Shop\Contracts\Controllers\API\CheckoutAPIControllerContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Http\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

class CheckoutAPIController implements CheckoutAPIControllerContract
{
    public function __construct(
        protected CheckoutServiceContract $checkout
    ){}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $orderData = $request->validated();

        $order = $this->checkout->saveOrder($orderData);

        $this->checkout->saveItems($order, $orderData['cartData']);

        $redirectUrl = $order->requiresOnlinePayment()
            ? $order->getOnlinePaymentUrl()
            : $order->getThankYouUrl();

        return new JsonResponse(data: [
            'redirectUrl' => $redirectUrl
        ], status: 201);
    }
}
