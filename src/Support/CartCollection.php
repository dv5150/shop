<?php

namespace DV5150\Shop\Support;

use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use Illuminate\Support\Collection;

class CartCollection extends Collection implements CartCollectionContract
{
    public function hasItem(SellableItemContract $item): bool
    {
        return $this->contains(fn (ShopItemCapsuleContract $capsule) => $capsule->getSellableItem()->is($item));
    }

    public function incrementQuantityBy(SellableItemContract $item, int $quantity): self
    {
        return $this->map(function (ShopItemCapsuleContract $capsule) use ($item, $quantity) {
            if ($capsule->getSellableItem()->is($item)) {
                return $capsule->setQuantity($capsule->getQuantity() + $quantity);
            }

            return $capsule;
        })->values();
    }

    public function decrementQuantityBy(SellableItemContract $item, int $quantity): self
    {
        return $this->map(function (ShopItemCapsuleContract $capsule) use ($item, $quantity) {
            if ($capsule->getSellableItem()->is($item)) {
                return $capsule->getQuantity() > $quantity
                    ? $capsule->setQuantity($capsule->getQuantity() - $quantity)
                    : null;
            }

            return $capsule;
        })->filter()
            ->values();
    }

    public function eraseItem(SellableItemContract $item): self
    {
        return $this->reject(fn (ShopItemCapsuleContract $capsule) => $capsule->getSellableItem()->is($item))
            ->filter()
            ->values();
    }

    public function refreshDiscounts(): self
    {
        $productKeys = collect($this->all())
            ->mapWithKeys(fn (ShopItemCapsuleContract $capsule) => [
                $capsule->getSellableItem()->getKey() => $capsule->getQuantity()
            ])->all();

        $products = config('shop.models.product')::with('discounts.discount')
            ->find(array_keys($productKeys));

        $capsules = $products->map(function (SellableItemContract $product) use ($productKeys) {
            return (new (config('shop.support.shopItemCapsule'))(
                sellableItem: $product,
                quantity: $productKeys[$product->getKey()]
            ))->applyBestDiscount();
        });

        return new static($capsules->all());
    }

    public function hasDigitalItemsOnly(): bool
    {
        return $this->doesntContain(
            fn (ShopItemCapsuleContract $capsule) => ! $capsule->getSellableItem()->isDigitalItem()
        );
    }

    public function getTotalGrossPrice(): float
    {
        return $this->sum(
            fn (ShopItemCapsuleContract $capsule) => $capsule->getSubtotalGrossPrice()
        );
    }
}
