<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\OrderContract;
use DV5150\Shop\Contracts\OrderDataTransformerContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Facades\Cart;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutService implements CheckoutServiceContract
{
    public function __construct(
        protected OrderDataTransformerContract $orderDataTransformer,
        protected OrderItemDataTransformerContract $orderItemDataTransformer
    ){}

    public function saveOrder(array $orderData): OrderContract
    {
        $orderData = $this->orderDataTransformer->transform($orderData);

        /** @var Model|OrderContract $order */
        $order = new (config('shop.models.order'))(
            array_merge($orderData, [
                'uuid' => $this->generateUniqueUuid()
            ])
        );

        if ($user = Auth::user()) {
            $order->user()->associate($user);
        }

        $order->shippingMode()->associate(
            config('shop.models.shippingMode')::firstWhere(
                'provider', $order->shipping_mode_provider
            )
        );

        $order->paymentMode()->associate(
            config('shop.models.paymentMode')::firstWhere(
                'provider', $order->payment_mode_provider
            )
        );

        $order->save();

        return $order;
    }

    public function saveItems(OrderContract $order, array $cartData): void
    {
        $quantities = Arr::pluck($cartData, 'quantity', 'item.id');

        $IDs = array_keys($quantities);

        $orderItems = config('shop.models.product')::with('discounts.discount')
            ->find($IDs)
            ->map(fn (ProductContract $product) => $this->makeOrderItem(
                (new CartItemCapsule($product, $quantities[$product->getKey()]))
                    ->applyDiscount()
            ));

        if ($coupon = Cart::getCoupon()) {
            $orderItems->push(
                $coupon->getCoupon()
                    ->toOrderItem($orderItems)
            );
        }

        if ($shippingMode = Cart::getShippingMode()) {
            $orderItems->push($shippingMode->toOrderItem());
        }

        if ($paymentMode = Cart::getPaymentMode()) {
            $orderItems->push($paymentMode->toOrderItem());
        }

        $order->items()->saveMany($orderItems);
    }

    protected function generateUniqueUuid(): string
    {
        do {
            $uuid = Str::uuid();
        } while (config('shop.models.order')::whereUuid($uuid)->exists());

        return $uuid;
    }

    protected function makeOrderItem(CartItemCapsule $capsule): OrderItemContract
    {
        $orderItem = new (config('shop.models.orderItem'))(
            $this->orderItemDataTransformer->transform($capsule)
        );

        $orderItem->product()->associate($capsule->getItem());

        return $orderItem;
    }
}