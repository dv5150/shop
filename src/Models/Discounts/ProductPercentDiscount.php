<?php

namespace DV5150\Shop\Models\Discounts;

use DV5150\Shop\Concerns\HasBaseDiscounts;
use DV5150\Shop\Concerns\ProvidesPercentDealData;
use DV5150\Shop\Contracts\Deals\DiscountContract;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Database\Eloquent\Model;

class ProductPercentDiscount extends Model implements DiscountContract
{
    use ProvidesPercentDealData,
        HasBaseDiscounts;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartItemCapsule $cartItem): float
    {
        $originalPrice = $cartItem->getOriginalProductPriceGross();

        $discount = $originalPrice * ($this->getValue() / 100);

        return max([$originalPrice - $discount, 0.0]);
    }

    public function getTypeName(): string
    {
        return 'Discount';
    }
}
