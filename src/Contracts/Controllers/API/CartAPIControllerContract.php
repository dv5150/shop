<?php

namespace DV5150\Shop\Contracts\Controllers\API;

use Illuminate\Http\JsonResponse;

interface CartAPIControllerContract
{
    public function index(): JsonResponse;
    public function store($productID, int $quantity = 1): JsonResponse;
    public function remove($productID, int $quantity = 1): JsonResponse;
    public function erase($productID): JsonResponse;
    public function setCoupon(string $couponCode): JsonResponse;
    public function removeCoupon(): JsonResponse;
    public function setShippingMode(string $shippingModeProvider): JsonResponse;
    public function setPaymentMode(string $paymentModeProvider): JsonResponse;
}