<?php

namespace DV5150\Shop\Models\Deals\Discounts;

use DV5150\Shop\Concerns\HasBaseDiscount;
use DV5150\Shop\Concerns\ProvidesValueDealData;
use DV5150\Shop\Contracts\Deals\DiscountContract;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Database\Eloquent\Model;

class ProductValueDiscount extends Model implements DiscountContract
{
    use ProvidesValueDealData,
        HasBaseDiscount;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartItemCapsule $cartItem): float
    {
        return max([
            $cartItem->getOriginalProductPriceGross() - $this->getValue(), 0.0
        ]);
    }

    public function getTypeName(): string
    {
        return 'Discount';
    }
}
