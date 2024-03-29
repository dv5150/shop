<?php

namespace DV5150\Shop\Models\Deals\Coupons;

use DV5150\Shop\Concerns\Deals\CouponTrait;
use DV5150\Shop\Contracts\Deals\Coupons\CouponContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Support\Collection;

class CartPercentCoupon extends Coupon implements CouponContract
{
    use CouponTrait;

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
        /** @var OrderItemContract $orderItem */
        $orderItem = new (config('shop.models.orderItem'))([
            'name' => $this->getName(),
            'quantity' => 1,
            'price_gross' => $this->calculateDiscountValue($orderItems),
            'type' => 'coupon',
            'info' => "Code: {$this->getCode()}",
        ]);

        $orderItem->sellable()->associate($this->getBaseCoupon());

        return $orderItem;
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

    public function getName(): string|null
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
