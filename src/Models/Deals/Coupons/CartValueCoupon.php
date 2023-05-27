<?php

namespace DV5150\Shop\Models\Deals\Coupons;

use DV5150\Shop\Concerns\HasBaseCoupons;
use DV5150\Shop\Concerns\ProvidesValueDealData;
use DV5150\Shop\Contracts\Deals\CouponContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CartValueCoupon extends Model implements CouponContract
{
    use ProvidesValueDealData,
        HasBaseCoupons;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float'
    ];

    public function getDiscountedPriceGross(CartCollection $cart): float
    {
        return max([$cart->getTotalGrossPrice() - $this->getValue(), 0.0]);
    }

    public function toOrderItem(Collection $orderItems): OrderItemContract
    {
        return new (config('shop.models.orderItem'))([
            'name' => $this->getFullName(),
            'quantity' => 1,
            'price_gross' => 0 - $this->getValue(),
        ]);
    }

    public function getTypeName(): string
    {
        return 'Coupon';
    }
}
