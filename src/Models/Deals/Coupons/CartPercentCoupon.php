<?php

namespace DV5150\Shop\Models\Deals\Coupons;

use DV5150\Shop\Concerns\HasBaseCoupon;
use DV5150\Shop\Concerns\ProvidesPercentDealData;
use DV5150\Shop\Contracts\Deals\CouponContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CartPercentCoupon extends Model implements CouponContract
{
    use ProvidesPercentDealData,
        HasBaseCoupon;

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

    public function toOrderItem(Collection $orderItems): OrderItemContract
    {
        return new (config('shop.models.orderItem'))([
            'name' => $this->getFullName(),
            'quantity' => 1,
            'price_gross' => $this->calculateDiscountValue($orderItems),
        ]);
    }

    protected function calculateDiscountValue(Collection $orderItems): float
    {
        return 0 - ($orderItems->sum(
            function (OrderItemContract $orderItem) {
                return $orderItem->getPriceGross()
                    * $orderItem->getQuantity();
            }
        ) * ($this->getValue() / 100));
    }

    public function getTypeName(): string
    {
        return 'Coupon';
    }
}
