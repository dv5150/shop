<?php

namespace DV5150\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingAddressResource extends JsonResource
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
            'displayName' => $this->getDisplayName(),
            'name' => $this->name,
            'zipCode' => $this->zip_code,
            'city' => $this->city,
            'street' => $this->address,
            'comment' => $this->comment,
        ];
    }

    protected function getDisplayName(): string
    {
        return "{$this->display_name} ({$this->zip_code} {$this->city}, {$this->address})";
    }
}
