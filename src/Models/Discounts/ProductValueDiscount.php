<?php

namespace DV5150\Shop\Models\Discounts;

use DV5150\Shop\Models\CartItemCapsule;
use DV5150\Shop\Models\Discount;

class ProductValueDiscount extends Discount
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartItemCapsule $cartItem): float
    {
        return max([
            $cartItem->getOriginalProductPriceGross() - $this->value, 0.0
        ]);
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
        return ':currency';
    }
}
