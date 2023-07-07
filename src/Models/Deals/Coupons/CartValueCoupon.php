<?php

namespace DV5150\Shop\Models\Deals\Coupons;

use DV5150\Shop\Concerns\Deals\HasBaseCoupon;
use DV5150\Shop\Contracts\Deals\Coupons\CouponContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CartValueCoupon extends Coupon implements CouponContract
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
        /** @var OrderItemContract $orderItem */
        $orderItem = new (config('shop.models.orderItem'))([
            'name' => $this->getName(),
            'quantity' => 1,
            'price_gross' => 0 - $this->getValue(),
            'type' => Str::kebab(class_basename($this)),
            'info' => "Code: {$this->getCode()}",
        ]);

        $orderItem->sellable()->associate($this->getBaseCoupon());

        return $orderItem;
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
