<?php

namespace DV5150\Shop\Models\Deals;

use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends BaseDeal implements BaseDiscountContract
{
    public $timestamps = false;

    protected $guarded = [];

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDiscountable(): ProductContract
    {
        return $this->discountable;
    }

    public function discount(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDiscount(): DiscountContract
    {
        return $this->discount;
    }

    public function getDiscountedPriceGross(CartItemCapsuleContract $capsule): float
    {
        return $this->getDiscount()
            ->getDiscountedPriceGross($capsule);
    }

    public function getName(): ?string
    {
        return $this->getDiscount()
            ->getName();
    }

    public function getValue(): float
    {
        return $this->getDiscount()
            ->getValue();
    }

    public function getUnit(): string
    {
        return $this->getDiscount()
            ->getUnit();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
