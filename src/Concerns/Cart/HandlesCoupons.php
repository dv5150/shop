<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Support\CartCollection;

trait HandlesCoupons
{
    public function setCoupon(?BaseCouponContract $coupon): CartCollection
    {
        $this->couponService->setCoupon($coupon);

        return $this->all();
    }

    public function getCoupon(): ?BaseCouponContract
    {
        return $this->couponService->getCoupon();
    }

    public function getCouponSummary(CartCollection $cartResults): ?array
    {
        $cart = $this->all();

        $coupon = $this->getCoupon();

        return $coupon ? [
            'couponItem' => $coupon,
            'couponDiscountAmount' => floor(
                $coupon->getDiscountedPriceGross($cart) - $cart->getTotalGrossPrice()
            ),
        ] : null;
    }
}