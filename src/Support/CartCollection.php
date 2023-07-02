<?php

namespace DV5150\Shop\Support;

use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use Illuminate\Support\Collection;

class CartCollection extends Collection
{
    public function hasItem(ProductContract $item): bool
    {
        return $this->contains(fn (CartItemCapsuleContract $capsule) => $capsule->getProduct()->is($item));
    }

    public function incrementQuantityBy(ProductContract $item, int $quantity): self
    {
        return $this->map(function (CartItemCapsuleContract $capsule) use ($item, $quantity) {
            if ($capsule->getProduct()->is($item)) {
                return $capsule->setQuantity($capsule->getQuantity() + $quantity);
            }

            return $capsule;
        })->values();
    }

    public function decrementQuantityBy(ProductContract $item, int $quantity): self
    {
        return $this->map(function (CartItemCapsuleContract $capsule) use ($item, $quantity) {
            if ($capsule->getProduct()->is($item)) {
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
        return $this->reject(fn (CartItemCapsuleContract $capsule) => $capsule->getProduct()->is($item))
            ->filter()
            ->values();
    }

    public function refreshDiscounts(): self
    {
        $productKeys = collect($this->all())
            ->mapWithKeys(fn (CartItemCapsuleContract $capsule) => [
                $capsule->getProduct()->getKey() => $capsule->getQuantity()
            ])->all();

        $products = config('shop.models.product')::with('discounts.discount')
            ->find(array_keys($productKeys));

        $capsules = $products->map(fn (ProductContract $product) => (new (config('shop.support.cartItemCapsule'))(
            product: $product,
            quantity: $productKeys[$product->getKey()]
        ))->applyBestDiscount());

        return new static($capsules->all());
    }

    public function hasDigitalItemsOnly(): bool
    {
        return $this->doesntContain(
            fn (CartItemCapsuleContract $capsule) => !$capsule->getProduct()->isDigitalProduct()
        );
    }

    public function getTotalGrossPrice(): float
    {
        return $this->sum(
            fn (CartItemCapsuleContract $capsule) => $capsule->getSubtotalGrossPrice()
        );
    }
}
