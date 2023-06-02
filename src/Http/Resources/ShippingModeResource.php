<?php

namespace DV5150\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingModeResource extends JsonResource
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
            'provider' => $this->getProvider(),
            'name' => $this->getName(),
            'priceGross' => $this->getPriceGross(),
            'componentName' => $this->getComponentName(),
        ];
    }
}
