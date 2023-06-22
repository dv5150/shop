<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Concerns\Cart\HandlesCoupons;
use DV5150\Shop\Concerns\Cart\HandlesPaymentModes;
use DV5150\Shop\Concerns\Cart\HandlesShippingModes;
use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
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

    public function all(): CartCollection
    {
        if ($cart = Session::get(self::SESSION_KEY)) {
            /** @var CartCollection $cart */
            $cart = unserialize($cart)
                ->map(fn (CartItemCapsuleContract $capsule) => $capsule->removeDiscount());

            return $cart->refreshDiscounts();
        }

        return $this->reset();
    }

    public function reset(): CartCollection
    {
        $cart = new CartCollection();

        $this->saveCart($cart);

        return $cart;
    }

    public function addItem(ProductContract $item, int $quantity = 1): CartCollection
    {
        $cart = $this->all();

        $cart = $cart->hasItem($item)
            ? $cart->incrementQuantityBy($item, $quantity)
            : $cart->push(
                new (config('shop.support.cartItemCapsule'))(
                    product: $item,
                    quantity: $quantity
                )
            );

        $this->saveCart($cart);

        return $this->all();
    }

    public function removeItem(ProductContract $item, int $quantity = 1): CartCollection
    {
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->decrementQuantityBy($item, $quantity);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function eraseItem(ProductContract $item): CartCollection
    {
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->eraseItem($item);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function getSubTotal(CartCollection $cartResults): float
    {
        return $cartResults->getTotalGrossPrice();
    }

    public function getTotal(CartCollection $cartResults): float
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

    public function saveCart(CartCollection $cart): CartCollection
    {
        Session::put(self::SESSION_KEY, serialize($cart));

        return $cart;
    }
}
