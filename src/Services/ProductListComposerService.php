<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Models\SellableItemContract;
use DV5150\Shop\Contracts\Services\ProductListComposerServiceContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;

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
                ->map(function (SellableItemContract $sellableItem) {
                    /** @var ShopItemCapsuleContract $shopItemCapsule */
                    $shopItemCapsule = (new (config('shop.support.shopItemCapsule'))(
                        sellableItem: $sellableItem,
                        quantity: 1
                    ));

                    return $shopItemCapsule->applyBestDiscount($sellableItem->discounts);
                })
            )->toJson()
        ];
    }
}