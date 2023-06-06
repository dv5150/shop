<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\PaymentModeContract;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class CartAPIController
{
    public function __construct(
        protected CartServiceContract $cart
    ){}

    #[Route("/api/shop/cart", methods: ["GET"])]
    public function index(): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->all()
        );
    }

    #[Route("/api/shop/cart/{product}/add/{quantity?}", methods: ["POST"])]
    public function store($productID, int $quantity = 1): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->addItem(
                $this->resolveProduct($productID),
                $quantity
            )
        );
    }

    #[Route("/api/shop/cart/{product}/remove/{quantity?}", methods: ["POST"])]
    public function remove($productID, int $quantity = 1): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->removeItem(
                $this->resolveProduct($productID),
                $quantity
            )
        );
    }

    #[Route("/api/shop/cart/{product}", methods: ["DELETE"])]
    public function erase($productID): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->eraseItem(
                $this->resolveProduct($productID)
            )
        );
    }

    #[Route("/api/shop/cart/coupon/{code}", methods: ["POST"])]
    public function setCoupon(string $couponCode): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setCoupon(
                $this->resolveCoupon($couponCode)
            )
        );
    }

    #[Route("/api/shop/cart/coupon", methods: ["DELETE"])]
    public function removeCoupon(): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setCoupon(null)
        );
    }

    #[Route("/api/shop/cart/shipping-mode/{provider}", methods: ["POST"])]
    public function setShippingMode(string $shippingModeProvider): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setShippingMode(
                $this->resolveShippingMode($shippingModeProvider)
            )
        );
    }

    #[Route("/api/shop/cart/payment-mode/{provider}", methods: ["POST"])]
    public function setPaymentMode(string $paymentModeProvider): JsonResponse
    {
        return $this->getCartResponse(
            $this->cart->setPaymentMode(
                $this->resolvePaymentMode($paymentModeProvider)
            )
        );
    }

    protected function getAllShippingModes(ShippingModeContract $selected): Collection
    {
        return config('shop.models.shippingMode')::with('paymentModes')
            ->get()
            ->map(fn (ShippingModeContract $shippingMode) => tap(
                $shippingMode,
                function (ShippingModeContract $shippingMode) use ($selected) {
                    $shippingMode->selected = $shippingMode->is($selected);
                })
            );
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
                'shippingMode' => config('shop.resources.shippingMode')::make(
                    $selectedShippingMode
                ),
                'paymentMode' => $selectedPaymentMode
                    ? config('shop.resources.paymentMode')::make(
                        $selectedPaymentMode
                    ) : null,
            ],
        ]);
    }

    protected function resolveProduct($productID): ProductContract
    {
        /** @var Model $product */
        $product = config('shop.models.product')::findOrFail($productID);

        if (!$product instanceof ProductContract) {
            abort(422, __('The selected item is not a valid product.'));
        }

        return $product;
    }

    protected function resolveCoupon(string $couponCode): ?Coupon
    {
        return Coupon::firstWhere('code', $couponCode);
    }

    protected function resolveShippingMode(string $shippingModeProvider): ShippingModeContract
    {
        return config('shop.models.shippingMode')::with('paymentModes')
            ->firstWhere('provider', $shippingModeProvider);
    }

    protected function resolvePaymentMode(string $paymentModeProvider): PaymentModeContract
    {
        return config('shop.models.paymentMode')::firstWhere('provider', $paymentModeProvider);
    }
}
