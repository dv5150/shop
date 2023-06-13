<?php

namespace DV5150\Shop\Models\Deals\Discounts;

use DV5150\Shop\Concerns\HasBaseDiscount;
use DV5150\Shop\Contracts\Deals\DiscountContract;
use DV5150\Shop\Contracts\Services\CartItemCapsuleContract;
use Illuminate\Database\Eloquent\Model;

class ProductValueDiscount extends Model implements DiscountContract
{
    use HasBaseDiscount;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartItemCapsuleContract $capsule): float
    {
        return max([
            $capsule->getOriginalProductPriceGross() - $this->getValue(), 0.0
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

    public function getShortName(): ?string
    {
        return "[DISCOUNT] {$this->getValue()} {$this->getUnit()}";
    }
}
