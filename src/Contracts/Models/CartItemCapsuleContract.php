<?php

namespace DV5150\Shop\Contracts\Models;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use Illuminate\Contracts\Support\Arrayable;

interface CartItemCapsuleContract extends Arrayable
{
    public function getProduct(): ProductContract;
    public function getQuantity(): int;
    public function setQuantity(int $quantity): self;
    public function getOriginalProductPriceGross(): float;
    public function getDiscount(): ?BaseDiscountContract;
    public function getPriceGross(): ?float;
    public function getSubtotalGrossPrice(): float;
    public function removeDiscount(): self;
    public function applyDiscount(): self;
}