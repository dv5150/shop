<?php

namespace DV5150\Shop\Models;

use DV5150\Shop\Contracts\ProductContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class CartItemCapsule implements Arrayable
{
    protected ProductContract $item;

    protected int $quantity;

    public function __construct(ProductContract $item, int $quantity)
    {
        $this->item = $item;
        $this->quantity = $quantity;
    }

    /**
     * @return ProductContract|Model
     */
    public function getItem(): ProductContract
    {
        return $this->item;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function toArray()
    {
        return [
            'item' => [
                'id' => $this->item->getID(),
                'name' => $this->item->getName(),
                'price_gross' => $this->item->getPriceGross(),
            ],
            'quantity' => $this->getQuantity(),
        ];
    }
}
