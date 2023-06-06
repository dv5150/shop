<?php

namespace DV5150\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->getName(),
            'price_gross' => $this->getPriceGross(),
            'discounts' => $this->discounts, // @todo: resource
            'categories' => $this->categories, // @todo: resource
        ];
    }
}
