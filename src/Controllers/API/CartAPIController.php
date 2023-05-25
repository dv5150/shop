<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;

class CartAPIController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function store($productID, int $quantity = 1): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::addItem($this->resolveProduct($productID), $quantity)
                    ->toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function remove($productID, int $quantity = 1): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::removeItem($this->resolveProduct($productID), $quantity)
                    ->toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function erase($productID): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::eraseItem($this->resolveProduct($productID))
                    ->toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function setCoupon(string $couponCode): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::setCoupon($this->resolveCoupon($couponCode))
                    ->toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    public function removeCoupon(): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => Cart::setCoupon(null)
                    ->toArray(),
                'coupon' => Cart::getCouponSummary(),
                'total' => Cart::getTotal(),
                'currency' => config('shop.currency'),
            ]
        ]);
    }

    protected function resolveProduct($productID): ProductContract
    {
        /** @var Model $product */
        $product = config('shop.models.product')::with('discounts.discount')
            ->findOrFail($productID);

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
