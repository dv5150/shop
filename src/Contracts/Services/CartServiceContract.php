<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;

interface CartServiceContract
{
    public function all(): CartCollectionContract;
    public function reset(): CartCollectionContract;
    public function addItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract;
    public function removeItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract;
    public function eraseItem(SellableItemContract $item): CartCollectionContract;

    public function getSubtotal(CartCollectionContract $cartResults): float;
    public function getTotal(CartCollectionContract $cartResults): float;
    public function hasDigitalItemsOnly(): bool;

    public function setCoupon(?BaseCouponContract $coupon): CartCollectionContract;
    public function getCoupon(): ?BaseCouponContract;
    public function getCouponSummary(CartCollectionContract $cartResults): ?array;

    public function setShippingMode(?ShippingModeContract $shippingMode): CartCollectionContract;
    public function getShippingMode(): ?ShippingModeContract;

    public function setPaymentMode(?PaymentModeContract $paymentMode): CartCollectionContract;
    public function getPaymentMode(): ?PaymentModeContract;

    public function saveCart(CartCollectionContract $cart): CartCollectionContract;
}
