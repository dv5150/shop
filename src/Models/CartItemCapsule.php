<?php

namespace DV5150\Shop\Models;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class CartItemCapsule implements Arrayable
{
    protected ?Discount $discount = null;

    protected ?float $discountedPriceGross = null;

    public function __construct(
        protected ProductContract $item,
        protected int $quantity
    ){}

    /**
     * @return ProductContract|Model
     */
    public function getItem(): ProductContract
    {
        return $this->item;
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
        return $this->getItem()->getPriceGross();
    }

    public function toArray()
    {
        return [
            'item' => [
                'id' => $this->getItem()->getKey(),
                'name' => $this->getItem()->getName(),
                'price_gross' => $this->getPriceGross(),
                'price_gross_original' => $this->getOriginalProductPriceGross(),
                'discount' => $this->getDiscount()?->toArray(),
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
        $this->getItem()
            ->discounts
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
