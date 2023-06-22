<?php

namespace DV5150\Shop\Models\Deals\Discounts;

use DV5150\Shop\Concerns\Deals\HasBaseDiscount;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
use DV5150\Shop\Models\Deals\Discount;

class ProductPercentDiscount extends Discount implements DiscountContract
{
    use HasBaseDiscount;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
    ];

    public function getDiscountedPriceGross(CartItemCapsuleContract $capsule): float
    {
        $originalPrice = $capsule->getOriginalProductPriceGross();

        $discount = $originalPrice * ($this->getValue() / 100);

        return max([$originalPrice - $discount, 0.0]);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return '%';
    }
}
