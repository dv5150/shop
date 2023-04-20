<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\OrderDataTransformerContract;
use DV5150\Shop\Contracts\OrderContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Requests\StoreOrderRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

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
            'redirectUrl' => route('shop.order.thankYou', ['order' => $order])
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

        $order->save();

        return $order;
    }

    protected function saveItems(OrderContract $order, array $cartData): void
    {
        $quantities = Arr::pluck($cartData, 'quantity', 'item.id');

        $IDs = array_keys($quantities);

        $orderItems = config('shop.models.product')::find($IDs)
            ->map(fn (ProductContract $product) => $this->makeOrderItem(
                $product,
                $quantities[$product->getID()]
            ));

        $order->items()->saveMany($orderItems);
    }

    protected function makeOrderItem(ProductContract $product, int $quantity): OrderItemContract
    {
        $orderItem = new (config('shop.models.orderItem'))(
            app(OrderItemDataTransformerContract::class)
                ->transform($product, $quantity)
        );

        $orderItem->product()->associate($product);

        return $orderItem;
    }
}
