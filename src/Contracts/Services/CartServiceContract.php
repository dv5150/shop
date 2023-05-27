<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Support\CartCollection;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\Deals\Coupon;

interface CartServiceContract
{
    public function all(): CartCollection;
    public function reset(): CartCollection;
    public function addItem(ProductContract $item, int $quantity = 1): CartCollection;
    public function removeItem(ProductContract $item, int $quantity = 1): CartCollection;
    public function eraseItem(ProductContract $item): CartCollection;
    public function setCoupon(?Coupon $coupon): CartCollection;
    public function getCouponSummary(CartCollection $cartResults): ?array;
    public function getCoupon(): ?Coupon;
    public function getTotal(CartCollection $cartResults): float;
    public function hasDigitalItemsOnly(): bool;
    public function toArray(): array;
    public function toJson($options = 0): string;
    public function saveCart(CartCollection $cart): CartCollection;
}
