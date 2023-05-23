<?php

namespace DV5150\Shop\Models\Coupons;

use DV5150\Shop\Concerns\ProvidesPercentDealData;
use DV5150\Shop\Contracts\Deals\CouponContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Model;

class CartPercentCoupon extends Model implements CouponContract
{
    use ProvidesPercentDealData;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartCollection $cart): float
    {
        $originalPrice = $cart->getTotalGrossPrice();

        $discount = $originalPrice * ($this->getValue() / 100);

        return max([$originalPrice - $discount, 0.0]);
    }
}
