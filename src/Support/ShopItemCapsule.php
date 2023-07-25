<?php

namespace DV5150\Shop\Support;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract;
use Illuminate\Support\Collection;

class ShopItemCapsule implements ShopItemCapsuleContract
{
    public function __construct(
        protected SellableItemContract $sellableItem,
        protected int $quantity = 1,
        protected ?BaseDiscountContract $discount = null,
        protected ?float $discountedPriceGross = null
    ){}

    public function getSellableItem(): SellableItemContract
    {
        return $this->sellableItem;
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

    public function getOriginalPriceGross(): float
    {
        return $this->getSellableItem()->getPriceGross();
    }

    public function toArray()
    {
        return [
            'item' => [
                'id' => $this->getSellableItem()->getKey(),
                'name' => $this->getSellableItem()->getName(),
                'price_gross' => (float) $this->getPriceGross(),
                'price_gross_original' => (float) $this->getOriginalPriceGross(),
                'discount' => $this->getDiscount()?->toArray(),
                'is_digital' => $this->getSellableItem()->isDigitalItem(),
            ],
            'quantity' => $this->getQuantity(),
            'subtotal' => $this->getSubtotalGrossPrice(),
        ];
    }

    public function getDiscount(): ?BaseDiscountContract
    {
        return $this->discount;
    }

    public function getPriceGross(): ?float
    {
        return $this->getDiscountedPriceGross()
            ?? $this->getOriginalPriceGross();
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

    public function applyBestDiscount(Collection $preLoadedDiscounts = null): self
    {
        $discounts = $preLoadedDiscounts ?? $this->getSellableItem()
            ->discounts()
            ->get();

        $discounts->each(fn (BaseDiscountContract $discount) => $this->tryDiscount($discount));

        return $this;
    }

    public function toOrderItem(): OrderItemContract
    {
        /** @var OrderItemContract $orderItem */
        $orderItem = new (config('shop.models.orderItem'))(
            app(OrderItemDataTransformerContract::class)->transform($this)
        );

        $orderItem->sellable()->associate($this->getSellableItem());

        return $orderItem;
    }

    protected function tryDiscount(BaseDiscountContract $discount): self
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
