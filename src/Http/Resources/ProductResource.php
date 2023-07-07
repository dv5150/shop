<?php

namespace DV5150\Shop\Http\Resources;

use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var ShopItemCapsuleContract $this */

        return [
            'id' => $this->getSellableItem()->getKey(),
            'name' => $this->getSellableItem()->getName(),
            'price_gross' => $this->getPriceGross(),
            'price_gross_original' => $this->getOriginalPriceGross(),
            'categories' => config('shop.resources.category')::collection(
                $this->getSellableItem()->categories
            ),
            'discount' => $this->getDiscount(),
        ];
    }
}
