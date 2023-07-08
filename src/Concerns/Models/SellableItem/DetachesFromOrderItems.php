<?php

namespace DV5150\Shop\Concerns\Models\SellableItem;

use DV5150\Shop\Contracts\Models\SellableItemContract;

trait DetachesFromOrderItems
{
    public static function bootDetachesFromOrderItems()
    {
        static::deleting(function (SellableItemContract $sellableItem) {
            config('shop.models.orderItem')::whereMorphedTo('sellable', $sellableItem)
                ->update([
                    'sellable_type' => null,
                    'sellable_id' => null,
                ]);
        });
    }
}