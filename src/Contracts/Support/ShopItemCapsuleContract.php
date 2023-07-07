<?php

namespace DV5150\Shop\Contracts\Support;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use Illuminate\Contracts\Support\Arrayable;

interface ShopItemCapsuleContract extends Arrayable
{
    public function getSellableItem(): SellableItemContract;

    public function getQuantity(): int;
    public function setQuantity(int $quantity): self;

    public function getOriginalPriceGross(): float;
    public function getPriceGross(): ?float;
    public function getSubtotalGrossPrice(): float;

    public function getDiscount(): ?BaseDiscountContract;
    public function applyBestDiscount(): self;
    public function removeDiscount(): self;
}