<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Support\CartCollection;

trait HandlesCoupons
{
    public function setCoupon(?Coupon $coupon): CartCollection
    {
        $this->couponService->setCoupon($coupon);

        return $this->all();
    }

    public function getCoupon(): ?Coupon
    {
        return $this->couponService->getCoupon();
    }

    public function getCouponSummary(CartCollection $cartResults): ?array
    {
        $cart = $this->all();

        $coupon = $this->getCoupon();

        return [
            'couponItem' => $coupon,
            'couponDiscountAmount' => floor(
                $coupon
                    ? $coupon->getDiscountedPriceGross($cart) - $cart->getTotalGrossPrice()
                    : null
            ),
        ];
    }
}