<?php

namespace DV5150\Shop\Controllers\API;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use DV5150\Shop\Http\Resources\ShippingModeResource;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Support\CartCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;

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

    protected function getCartResponse(CartCollection $cartResults): JsonResponse
    {
        return new JsonResponse(data: [
            'cart' => [
                'items' => $cartResults->toArray(),
                'coupon' => $this->cart->getCouponSummary($cartResults),
                'subtotal' => $this->cart->getSubtotal($cartResults),
                'total' => $this->cart->getTotal($cartResults),
                'currency' => config('shop.currency'),
                'shippingMode' => new ShippingModeResource(
                    $this->cart->getShippingMode()
                ),
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
        return config('shop.models.shippingMode')::firstWhere('provider', $shippingModeProvider);
    }
}
