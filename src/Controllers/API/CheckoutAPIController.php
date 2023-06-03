<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

class CheckoutAPIController
{
    public function __construct(
        protected CheckoutServiceContract $checkoutService
    ){}

    #[Route("/api/shop/checkout/shipping-modes", methods: ["GET"])]
    public function shippingModes(): JsonResponse
    {
        return new JsonResponse(data: [
            'shippingModes' => config('shop.resources.shippingMode')::collection(
                config('shop.models.shippingMode')::all()
            )
        ]);
    }

    #[Route("/api/shop/checkout/payment-modes", methods: ["GET"])]
    public function paymentModes(): JsonResponse
    {
        return new JsonResponse(data: [
            'paymentModes' => config('shop.models.paymentMode')::all()
        ]);
    }

    #[Route("/api/shop/checkout", methods: ["POST"])]
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $orderData = $request->validated();

        $order = $this->checkoutService->saveOrder($orderData);

        $this->checkoutService->saveItems($order, $orderData['cartData']);

        return new JsonResponse(data: [
            'redirectUrl' => $order->getThankYouUrl()
        ], status: 201);
    }
}
