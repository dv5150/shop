<?php

namespace DV5150\Shop\Models\Deals\Discounts;

use DV5150\Shop\Concerns\Deals\DiscountTrait;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Models\Deals\Discount;

class ProductPercentDiscount extends Discount implements DiscountContract
{
    use DiscountTrait;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
    ];

    public function getDiscountedPriceGross(ShopItemCapsuleContract $capsule): float
    {
        $originalPrice = $capsule->getOriginalPriceGross();

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
