<?php

namespace DV5150\Shop\Contracts\Support;

use DV5150\Shop\Contracts\Models\SellableItemContract;

interface CartCollectionContract
{
    public function hasItem(SellableItemContract $item): bool;
    public function incrementQuantityBy(SellableItemContract $item, int $quantity): self;
    public function decrementQuantityBy(SellableItemContract $item, int $quantity): self;
    public function eraseItem(SellableItemContract $item): self;
    public function refreshDiscounts(): self;
    public function hasDigitalItemsOnly(): bool;
    public function getTotalGrossPrice(): float;
}