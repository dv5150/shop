<?php

namespace DV5150\Shop\Models\Deals;

use DV5150\Shop\Contracts\Deals\BaseDealContract;
use DV5150\Shop\Contracts\Deals\CouponContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Coupon extends Model implements BaseDealContract
{
    public $timestamps = false;

    protected $guarded = [];

    public function coupon(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCoupon(): CouponContract
    {
        return $this->coupon;
    }

    public function getDiscountedPriceGross(CartCollection $cart): float
    {
        return $this->getCoupon()
            ->getDiscountedPriceGross($cart);
    }

    public function getFullName(): ?string
    {
        return $this->getCoupon()
            ->getFullName();
    }

    public function getName(): ?string
    {
        return $this->getCoupon()
            ->getName();
    }

    public function getValue(): float
    {
        return $this->getCoupon()
            ->getValue();
    }

    public function getUnit(): string
    {
        return $this->getCoupon()
            ->getUnit();
    }

    public function toArray(): array
    {
        return [
            'fullName' => $this->getFullName(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
