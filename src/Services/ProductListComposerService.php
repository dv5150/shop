<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Contracts\Services\ProductListComposerServiceContract;

class ProductListComposerService implements ProductListComposerServiceContract
{
    public function getProductListData(): array
    {
        return [
            'products' => config('shop.resources.product')::collection(
                config('shop.models.product')::with([
                    'categories', 'discounts.discount'
                ])
                ->get()
                ->map(function (ProductContract $product) {
                    /** @var CartItemCapsuleContract $cartItemCapsule */
                    $cartItemCapsule = (new (config('shop.support.cartItemCapsule'))(
                        product: $product,
                        quantity: 1
                    ));

                    return $cartItemCapsule->applyBestDiscount($product->discounts);
                })
            )->toJson()
        ];
    }
}