<?php

namespace DV5150\Shop\Http\Resources;

use DV5150\Shop\Contracts\Models\CartItemCapsuleContract;
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
        /** @var CartItemCapsuleContract $this */

        return [
            'name' => $this->getProduct()->getName(),
            'price_gross' => $this->getPriceGross(),
            'price_gross_original' => $this->getOriginalProductPriceGross(),
            'categories' => config('shop.resources.category')::collection(
                $this->getProduct()->categories
            ),
            'discount' => $this->getDiscount(),
        ];
    }
}
