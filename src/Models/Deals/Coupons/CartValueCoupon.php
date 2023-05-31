<?php

namespace DV5150\Shop\Models\Deals\Coupons;

use DV5150\Shop\Concerns\HasBaseCoupon;
use DV5150\Shop\Contracts\Deals\CouponContract;
use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CartValueCoupon extends Model implements CouponContract
{
    use HasBaseCoupon;

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
            'name' => $this->getShortName(),
            'quantity' => 1,
            'price_gross' => 0 - $this->getValue(),
            'info' => "[COUPON] [Code: {$this->getCode()}]",
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
        return trim("[COUPON -{$this->getValue()} {$this->getUnit()}] {$this->getName()}");
    }
}
