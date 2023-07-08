<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Concerns\Cart\HandlesCoupons;
use DV5150\Shop\Concerns\Cart\HandlesPaymentModes;
use DV5150\Shop\Concerns\Cart\HandlesShippingModes;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Support\Facades\Session;

class CartService implements CartServiceContract
{
    use HandlesCoupons,
        HandlesShippingModes,
        HandlesPaymentModes;

    protected const SESSION_KEY = 'cart';

    public function __construct(
        protected CouponServiceContract $couponService,
        protected ShippingModeServiceContract $shippingModeService,
        protected PaymentModeServiceContract $paymentModeService,
    ){}

    public function all(): CartCollectionContract
    {
        if ($cart = Session::get(self::SESSION_KEY)) {
            /** @var CartCollection $cart */
            $cart = unserialize($cart)
                ->map(fn (ShopItemCapsuleContract $capsule) => $capsule->removeDiscount());

            return $cart->refreshDiscounts();
        }

        return $this->reset();
    }

    public function reset(): CartCollectionContract
    {
        $cart = app(CartCollectionContract::class);

        $this->saveCart($cart);

        return $cart;
    }

    public function addItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract
    {
        $cart = $this->all();

        $cart = $cart->hasItem($item)
            ? $cart->incrementQuantityBy($item, $quantity)
            : $cart->push(
                new (config('shop.support.shopItemCapsule'))(
                    sellableItem: $item,
                    quantity: $quantity
                )
            );

        $this->saveCart($cart);

        return $this->all();
    }

    public function removeItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract
    {
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->decrementQuantityBy($item, $quantity);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function eraseItem(SellableItemContract $item): CartCollectionContract
    {
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->eraseItem($item);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function getSubTotal(CartCollectionContract $cartResults): float
    {
        return $cartResults->getTotalGrossPrice();
    }

    public function getTotal(CartCollectionContract $cartResults): float
    {
        $coupon = $this->getCoupon();

        $grossTotal = $coupon
            ? floor($coupon->getDiscountedPriceGross($cartResults))
            : $cartResults->getTotalGrossPrice();

        return $grossTotal
            + $this->getShippingMode()?->getPriceGross()
            + $this->getPaymentMode()?->getPriceGross();
    }

    public function hasDigitalItemsOnly(): bool
    {
        return $this->all()
            ->hasDigitalItemsOnly();
    }

    public function saveCart(CartCollectionContract $cart): CartCollectionContract
    {
        Session::put(self::SESSION_KEY, serialize($cart));

        return $cart;
    }
}
