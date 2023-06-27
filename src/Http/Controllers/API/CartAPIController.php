<?php

namespace DV5150\Shop\Http\Controllers\API;

use DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\MessageServiceContract;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class CartAPIController
{
    public function __construct(
        protected CartServiceContract $cart,
        protected MessageServiceContract $messages,
    ){}

    /** GET /api/shop/cart */
    public function index(): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->all()
        );
    }

    /** POST /api/shop/cart/{product}/add/{quantity?} */
    public function store($productID, int $quantity = 1): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->addItem(
                $this->resolveProduct($productID),
                $quantity
            )
        );
    }

    /** POST /api/shop/cart/{product}/remove/{quantity?} */
    public function remove($productID, int $quantity = 1): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->removeItem(
                $this->resolveProduct($productID),
                $quantity
            )
        );
    }

    /** DELETE /api/shop/cart/{product} */
    public function erase($productID): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->eraseItem(
                $this->resolveProduct($productID)
            )
        );
    }

    /** POST /api/shop/cart/coupon/{code} */
    public function setCoupon(string $couponCode): JsonResponse
    {
        $coupon = $this->resolveCoupon($couponCode);

        if (! $coupon) {
            $this->messages->addNegativeMessage('coupon.404', __('Coupon not found.'));

            return $this->index();
        }

        return $this->getCartResponse(
            $this->cart->setCoupon($coupon)
        );
    }

    /** DELETE /api/shop/cart/coupon */
    public function removeCoupon(): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setCoupon(null)
        );
    }

    /** POST /api/shop/cart/shipping-mode/{provider} */
    public function setShippingMode(string $shippingModeProvider): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setShippingMode(
                $this->resolveShippingMode($shippingModeProvider)
            )
        );
    }

    /** POST /api/shop/cart/payment-mode/{provider} */
    public function setPaymentMode(string $paymentModeProvider): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setPaymentMode(
                $this->resolvePaymentMode($paymentModeProvider)
            )
        );
    }

    protected function getAllShippingModes(?ShippingModeContract $selected = null): Collection
    {
        $shippingModes = config('shop.models.shippingMode')::with('paymentModes')
            ->get();

        if ($selected) {
            return $shippingModes->map(fn (ShippingModeContract $shippingMode) => tap(
                $shippingMode,
                function (ShippingModeContract $shippingMode) use ($selected) {
                    $shippingMode->selected = $shippingMode->is($selected);
                })
            );
        }

        return $shippingModes;
    }

    protected function getCartResponse(CartCollection $cartResults): JsonResponse
    {
        $selectedShippingMode = $this->cart->getShippingMode();
        $selectedPaymentMode = $this->cart->getPaymentMode();

        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'subtotal' => $this->cart->getSubtotal($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
                'availableShippingModes' => config('shop.resources.shippingMode')::collection(
                    $this->getAllShippingModes($selectedShippingMode)
                ),
                'shippingMode' => $selectedShippingMode
                    ? config('shop.resources.shippingMode')::make($selectedShippingMode)
                    : null,
                'paymentMode' => $selectedPaymentMode
                    ? config('shop.resources.paymentMode')::make($selectedPaymentMode)
                    : null,
                'messages' => $this->messages->all(),
            ],
        ]);
    }

    private function resolveProduct($productID): ProductContract
    {
        /** @var Model $product */
        $product = config('shop.models.product')::findOrFail($productID);

        if (!$product instanceof ProductContract) {
            abort(422, __('The selected item is not a valid product.'));
        }

        return $product;
    }

    private function resolveCoupon(string $couponCode): ?BaseCouponContract
    {
        return config('shop.models.coupon')::firstWhere('code', $couponCode);
    }

    private function resolveShippingMode(string $shippingModeProvider): ?ShippingModeContract
    {
        return config('shop.models.shippingMode')::with('paymentModes')
            ->firstWhere('provider', $shippingModeProvider);
    }

    private function resolvePaymentMode(string $paymentModeProvider): ?PaymentModeContract
    {
        if ($shippingMode = $this->cart->getShippingMode()) {
            return $shippingMode->paymentModes()
                ->firstWhere('provider', $paymentModeProvider);
        }

        return null;
    }
}
