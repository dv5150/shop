<?php

namespace DV5150\Shop\Models;

use DV5150\Shop\Contracts\ProductContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class CartItemCapsule implements Arrayable
{
    protected ProductContract $item;

    protected int $quantity;

    protected ?Discount $bestDiscount = null;

    protected ?float $discountedPriceGross = null;

    public function __construct(ProductContract $item, int $quantity)
    {
        $this->item = $item;
        $this->quantity = $quantity;
    }

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
        return $this->item->getPriceGross();
    }

    public function toArray()
    {
        return [
            'item' => [
                'id' => $this->item->getID(),
                'name' => $this->item->getName(),
                'price_gross' => $this->getPriceGross(),
                'price_gross_original' => $this->getOriginalProductPriceGross(),
                'discount' => $this->getBestDiscount()?->toArray(),
            ],
            'quantity' => $this->getQuantity(),
        ];
    }

    public function refreshDiscount(): self
    {
        $this->removeDiscount();

        $this->getItem()->load('discounts.discount');

        $this->applyDiscount();

        return $this;
    }

    protected function applyDiscount(): self
    {
        $this->getItem()->discounts->each(
            fn (Discount $discount) => $this->setDiscount($discount->discount)
        );

        return $this;
    }

    protected function setDiscount(Discount $discount): self
    {
        $newDiscountedPriceGross = $discount->getDiscountedPriceGross($this);

        if ($this->discountedPriceGross <= $newDiscountedPriceGross) {
            $this->bestDiscount = $discount;
            $this->discountedPriceGross = $newDiscountedPriceGross;
        }

        return $this;
    }

    protected function removeDiscount(): self
    {
        $this->bestDiscount = null;
        $this->discountedPriceGross = null;

        return $this;
    }

    protected function getBestDiscount(): ?Discount
    {
        return $this->bestDiscount;
    }

    protected function getDiscountedPriceGross(): ?float
    {
        return $this->discountedPriceGross;
    }

    protected function getPriceGross(): float
    {
        return $this->getDiscountedPriceGross()
            ?? $this->getOriginalProductPriceGross();
    }
}
