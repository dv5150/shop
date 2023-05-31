<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Support\CartCollection;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use DV5150\Shop\Models\CartItemCapsule;
use DV5150\Shop\Models\Deals\Coupon;
use Illuminate\Support\Facades\Session;

class CartService implements CartServiceContract
{
    protected CouponServiceContract $couponService;
    protected ShippingModeServiceContract $shippingModeService;

    public function __construct(
        CouponServiceContract $couponService,
        ShippingModeServiceContract $shippingModeService
    ) {
        $this->couponService = $couponService;
        $this->shippingModeService = $shippingModeService;
    }

    public function all(): CartCollection
    {
        if ($cart = Session::get($this->getSessionKey())) {
            return unserialize($cart)
                ->map(fn (CartItemCapsule $capsule) => $capsule->removeDiscount())
                ->refreshDiscounts();
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
            : $cart->push(new CartItemCapsule($item, $quantity));

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

    public function setCoupon(?Coupon $coupon): CartCollection
    {
        $this->couponService->setCoupon($coupon);

        return $this->all();
    }

    public function getCoupon(): ?Coupon
    {
        return $this->couponService->getCoupon();
    }

    public function getCouponSummary(CartCollection $cartResults): ?array
    {
        $cart = $this->all();

        $coupon = $this->getCoupon();

        $couponDiscountAmount = $coupon
            ? $coupon->getDiscountedPriceGross($cart) - $cart->getTotalGrossPrice()
            : null;

        return [
            'couponItem' => $coupon,
            'couponDiscountAmount' => floor($couponDiscountAmount),
        ];
    }

    public function setShippingMode(ShippingModeContract $shippingMode): CartCollection
    {
        $this->shippingModeService->setShippingMode($shippingMode);

        return $this->all();
    }

    public function getShippingMode(): ShippingModeContract
    {
        return $this->shippingModeService->getShippingMode();
    }

    public function getTotal(CartCollection $cartResults): float
    {
        $coupon = $this->getCoupon();

        $grossTotal = $coupon
            ? floor($coupon->getDiscountedPriceGross($cartResults))
            : $cartResults->getTotalGrossPrice();

        return $grossTotal + $this->getShippingMode()->getPriceGross();
    }

    public function hasDigitalItemsOnly(): bool
    {
        return $this->all()
            ->hasDigitalItemsOnly();
    }

    public function saveCart(CartCollection $cart): CartCollection
    {
        Session::put($this->getSessionKey(), serialize($cart));

        return $cart;
    }

    public function toArray(): array
    {
        return $this->all()
            ->toArray();
    }

    public function toJson($options = 0): string
    {
        return $this->all()
            ->toJson($options);
    }

    protected function getSessionKey(): string
    {
        return 'cart';
    }
}
