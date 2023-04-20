<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Contracts\ProductContract;
use Illuminate\Http\JsonResponse;

class CartAPIController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(data: [
            'cartItems' => Cart::toArray()
        ]);
    }

    public function store($productID, int $quantity = 1): JsonResponse
    {
        return new JsonResponse(data: [
            'cartItems' => Cart::addItem($this->resolveProduct($productID), $quantity)
                ->toArray()
        ]);
    }

    public function remove($productID, int $quantity = 1): JsonResponse
    {
        return new JsonResponse(data: [
            'cartItems' => Cart::removeItem($this->resolveProduct($productID), $quantity)
                ->toArray()
        ]);
    }

    public function erase($productID): JsonResponse
    {
        return new JsonResponse(data: [
            'cartItems' => Cart::eraseItem($this->resolveProduct($productID))
                ->toArray()
        ]);
    }

    protected function resolveProduct($productID): ProductContract
    {
        $product = config('shop.models.product')::findOrFail($productID);

        if (!$product instanceof ProductContract) {
            abort(422, __('The selected item is not a valid product.'));
        }

        return $product;
    }
}
