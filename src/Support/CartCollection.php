<?php

namespace DV5150\Shop\Support;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Support\Collection;

class CartCollection extends Collection
{
    public function hasItem(ProductContract $item): bool
    {
        return $this->contains(fn (CartItemCapsule $capsule) => $capsule->getItem()->is($item));
    }

    public function incrementQuantityBy(ProductContract $item, int $quantity): self
    {
        return $this->map(function (CartItemCapsule $capsule) use ($item, $quantity) {
            if ($capsule->getItem()->is($item)) {
                return $capsule->setQuantity($capsule->getQuantity() + $quantity);
            }

            return $capsule;
        })->values();
    }

    public function decrementQuantityBy(ProductContract $item, int $quantity): self
    {
        return $this->map(function (CartItemCapsule $capsule) use ($item, $quantity) {
            if ($capsule->getItem()->is($item)) {
                return $capsule->getQuantity() > $quantity
                    ? $capsule->setQuantity($capsule->getQuantity() - $quantity)
                    : null;
            }

            return $capsule;
        })->filter()
            ->values();
    }

    public function eraseItem(ProductContract $item): self
    {
        return $this->reject(fn (CartItemCapsule $capsule) => $capsule->getItem()->is($item))
            ->filter()
            ->values();
    }

    public function refreshDiscounts(): self
    {
        $productKeys = collect($this->all())
            ->mapWithKeys(fn (CartItemCapsule $capsule) => [
                $capsule->getItem()->getKey() => $capsule->getQuantity()
            ])->all();

        $products = config('shop.models.product')::with('discounts.discount')
            ->find(array_keys($productKeys));

        $capsules = $products->map(function (ProductContract $product) use ($productKeys) {
            return (new CartItemCapsule($product, $productKeys[$product->getKey()]))
                ->applyDiscount();
        });

        return new static($capsules->all());
    }

    public function hasDigitalItemsOnly(): bool
    {
        return $this->doesntContain(
            fn (CartItemCapsule $capsule) => !$capsule->getItem()->isDigitalProduct()
        );
    }

    public function getTotalGrossPrice(): float
    {
        return $this->sum(
            fn (CartItemCapsule $capsule) => $capsule->getSubtotalGrossPrice()
        );
    }
}
