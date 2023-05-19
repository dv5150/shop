<?php

namespace DV5150\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function discount(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDiscountedPriceGross(CartItemCapsule $capsule): float
    {
        return $this->discount->getDiscountedPriceGross($capsule);
    }

    public function getFullName(): ?string
    {
        return $this->discount->getFullName();
    }

    public function getName(): ?string
    {
        return $this->discount->getName();
    }

    public function getValue(): float
    {
        return $this->discount->getValue();
    }

    public function getUnit(): string
    {
        return $this->discount->getUnit();
    }

    public function toArray(): array
    {
        return [
            'fullName' => $this->getFullName(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
