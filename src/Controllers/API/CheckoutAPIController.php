<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\OrderDataTransformerContract;
use DV5150\Shop\Contracts\OrderContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\CartItemCapsule;
use DV5150\Shop\Requests\StoreOrderRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CheckoutAPIController
{
    public function shippingModes(): JsonResponse
    {
        return new JsonResponse(data: [
            'shippingModes' => config('shop.models.shippingMode')::all()
        ]);
    }

    public function paymentModes(): JsonResponse
    {
        return new JsonResponse(data: [
            'paymentModes' => config('shop.models.paymentMode')::all()
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $orderData = $request->validated();

        $order = $this->saveOrder($orderData);

        $this->saveItems($order, $orderData['cartData']);

        return new JsonResponse(data: [
            'redirectUrl' => $order->getThankYouUrl()
        ], status: 201);
    }

    protected function makeOrder(array $orderData): OrderContract
    {
        return new (config('shop.models.order'))(
            app(OrderDataTransformerContract::class)
                ->transform($orderData)
        );
    }

    protected function saveOrder(array $orderData): OrderContract
    {
        /** @var Model|OrderContract $order */
        $order = $this->makeOrder($orderData);

        if ($user = Auth::user()) {
            $order->user()->associate($user);
        }

        do {
            $uuid = Str::uuid();
        } while (config('shop.models.order')::whereUuid($uuid)->exists());

        $order->setAttribute('uuid', $uuid);
        $order->save();

        return $order;
    }

    protected function saveItems(OrderContract $order, array $cartData): void
    {
        $quantities = Arr::pluck($cartData, 'quantity', 'item.id');

        $IDs = array_keys($quantities);

        $orderItems = config('shop.models.product')::with('discounts.discount')
            ->find($IDs)
            ->map(fn (ProductContract $product) => $this->makeOrderItem(
                (new CartItemCapsule($product, $quantities[$product->getID()]))
                    ->applyDiscount()
            ));

        if ($coupon = Cart::getCoupon()) {
            $orderItems->push(
                $coupon->getCoupon()
                    ->toOrderItem($orderItems)
            );
        }

        $order->items()->saveMany($orderItems);
    }

    protected function makeOrderItem(CartItemCapsule $capsule): OrderItemContract
    {
        $orderItem = new (config('shop.models.orderItem'))(
            app(OrderItemDataTransformerContract::class)
                ->transform($capsule)
        );

        $orderItem->product()->associate($capsule->getItem());

        return $orderItem;
    }
}
