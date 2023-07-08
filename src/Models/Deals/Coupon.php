<?php

namespace DV5150\Shop\Models\Deals;

use DV5150\Shop\Concerns\Deals\BaseCouponTrait;
use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Deals\Coupons\CouponContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Coupon extends BaseDeal implements BaseCouponContract
{
    use BaseCouponTrait;

    public $timestamps = false;

    protected $guarded = [];

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = Str::of($value)->upper();
    }

    public function coupon(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCoupon(): CouponContract
    {
        return $this->coupon;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDiscountedPriceGross(CartCollection $cart): float
    {
        return $this->getCoupon()
            ->getDiscountedPriceGross($cart);
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
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
