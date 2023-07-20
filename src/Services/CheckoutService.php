<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Contracts\Transformers\OrderDataTransformerContract;
use DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract;
use DV5150\Shop\Facades\Cart;
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

        Arr::set($orderData, 'uuid', $this->generateUniqueUuid());

        /** @var Model|OrderContract $order */
        $order = new (config('shop.models.order'))(Arr::except($orderData, [
            'shipping_mode_provider',
            'payment_mode_provider',
        ]));

        if ($user = Auth::user()) {
            $order->user()->associate($user);
        }

        $order->shippingMode()->associate($this->resolveShippingMode($orderData['shipping_mode_provider']));
        $order->paymentMode()->associate($this->resolvePaymentMode($orderData['payment_mode_provider']));

        $order->save();

        return $order;
    }

    public function saveItems(OrderContract $order, array $cartData): void
    {
        $quantities = Arr::pluck($cartData, 'quantity', 'item.id');

        $IDs = array_keys($quantities);

        $orderItems = config('shop.models.product')::with('discounts.discount')
            ->find($IDs)
            ->map(fn (SellableItemContract $product) => $this->makeOrderItem(
                (new (config('shop.support.shopItemCapsule'))(
                    sellableItem: $product,
                    quantity: $quantities[$product->getKey()]
                ))->applyBestDiscount()
            ));

        /** @var BaseCouponContract $coupon */
        if ($coupon = Cart::getCoupon()) {
            $orderItems->push(
                $coupon->getCoupon()
                    ->toOrderItem($orderItems)
            );
        }

        /** @var ShippingModeContract $shippingMode */
        if ($shippingMode = Cart::getShippingMode()) {
            $orderItems->push($shippingMode->toOrderItem());
        }

        /** @var PaymentModeContract $paymentMode */
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

    protected function makeOrderItem(ShopItemCapsuleContract $capsule): OrderItemContract
    {
        /** @var OrderItemContract $orderItem */
        $orderItem = new (config('shop.models.orderItem'))(
            $this->orderItemDataTransformer->transform($capsule)
        );

        $orderItem->sellable()->associate($capsule->getSellableItem());

        return $orderItem;
    }

    protected function resolveShippingMode(string $provider): ShippingModeContract
    {
        return config('shop.models.shippingMode')::firstWhere('provider', $provider);
    }

    protected function resolvePaymentMode(string $provider): PaymentModeContract
    {
        return config('shop.models.paymentMode')::firstWhere('provider', $provider);
    }
}