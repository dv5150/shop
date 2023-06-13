<?php

namespace DV5150\Shop\Support;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;
use DV5150\Shop\Models\Deals\Discount;

class CartItemCapsule implements CartItemCapsuleContract
{
    public function __construct(
        protected ProductContract $product,
        protected int $quantity,
        protected ?Discount $discount = null,
        protected ?float $discountedPriceGross = null
    ){}

    public function getProduct(): ProductContract
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOriginalProductPriceGross(): float
    {
        return $this->getProduct()->getPriceGross();
    }

    public function toArray()
    {
        return [
            'item' => [
                'id' => $this->getProduct()->getKey(),
                'name' => $this->getProduct()->getName(),
                'price_gross' => $this->getPriceGross(),
                'price_gross_original' => $this->getOriginalProductPriceGross(),
                'discount' => $this->getDiscount()?->toArray(),
                'is_digital' => $this->getProduct()->isDigitalProduct(),
            ],
            'quantity' => $this->getQuantity(),
            'subtotal' => $this->getSubtotalGrossPrice(),
        ];
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function getPriceGross(): ?float
    {
        return $this->getDiscountedPriceGross()
            ?? $this->getOriginalProductPriceGross();
    }

    public function getSubtotalGrossPrice(): float
    {
        return $this->getPriceGross() * $this->getQuantity();
    }

    public function removeDiscount(): self
    {
        $this->discount = null;
        $this->discountedPriceGross = null;

        return $this;
    }

    public function applyDiscount(): self
    {
        $this->getProduct()
            ->discounts()
            ->get()
            ->each(fn (Discount $discount) => $this->tryDiscount($discount));

        return $this;
    }

    protected function tryDiscount(Discount $discount): self
    {
        $newDiscountedPriceGross = $discount->getDiscountedPriceGross($this);

        if ($newDiscountedPriceGross <= $this->getPriceGross()) {
            $this->discountedPriceGross = $newDiscountedPriceGross;
            $this->discount = $discount;
        }

        return $this;
    }

    protected function getDiscountedPriceGross(): ?float
    {
        return $this->discountedPriceGross
            ? floor($this->discountedPriceGross)
            : null;
    }
}
