<?php

namespace DV5150\Shop\Models\Deals\Discounts;

use DV5150\Shop\Concerns\Deals\HasBaseDiscount;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use DV5150\Shop\Models\Deals\Discount;

class ProductValueDiscount extends Discount implements DiscountContract
{
    use HasBaseDiscount;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(ShopItemCapsuleContract $capsule): float
    {
        return max([
            $capsule->getOriginalPriceGross() - $this->getValue(), 0.0
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
        return config('shop.currency.code');
    }
}
