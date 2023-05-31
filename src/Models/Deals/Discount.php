<?php

namespace DV5150\Shop\Models\Deals;

use DV5150\Shop\Contracts\Deals\BaseDealContract;
use DV5150\Shop\Contracts\Deals\DiscountContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model implements BaseDealContract
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

    public function getDiscountedPriceGross(CartItemCapsule $capsule): float
    {
        return $this->getDiscount()
            ->getDiscountedPriceGross($capsule);
    }

    public function getShortName(): ?string
    {
        return $this->getDiscount()
            ->getShortName();
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
            'shortName' => $this->getShortName(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
