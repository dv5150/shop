<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;

class CartAPIController
{
    protected CartServiceContract $cart;

    public function __construct(CartServiceContract $cart)
    {
        $this->cart = $cart;
    }

    public function index(): JsonResponse
    {
        $cartResults = $this->cart->all();

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function store($productID, int $quantity = 1): JsonResponse
    {
        $cartResults = $this->cart->addItem(
            $this->resolveProduct($productID),
            $quantity
        );

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function remove($productID, int $quantity = 1): JsonResponse
    {
        $cartResults = $this->cart->removeItem(
            $this->resolveProduct($productID),
            $quantity
        );

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function erase($productID): JsonResponse
    {
        $cartResults = $this->cart->eraseItem($this->resolveProduct($productID));

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function setCoupon(string $couponCode): JsonResponse
    {
        $cartResults = $this->cart->setCoupon($this->resolveCoupon($couponCode));

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function removeCoupon(): JsonResponse
    {
        $cartResults = $this->cart->setCoupon(null);

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    protected function resolveProduct($productID): ProductContract
    {
        /** @var Model $product */
        $product = config('shop.models.product')::findOrFail($productID);

        if (!$product instanceof ProductContract) {
            abort(422, __('The selected item is not a valid product.'));
        }

        return $product;
    }

    protected function resolveCoupon(string $couponCode): ?Coupon
    {
        return Coupon::firstWhere('code', $couponCode);
    }
}
