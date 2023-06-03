<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\OrderItemContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMode extends Model implements ShippingModeContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_gross' => 'float',
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return "[SHIPPING]";
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }

    public function getComponentName(): ?string
    {
        return $this->component_name;
    }

    public function toOrderItem(): OrderItemContract
    {
        return new (config('shop.models.orderItem'))([
            'name' => $this->getName(),
            'quantity' => 1,
            'price_gross' => $this->getPriceGross(),
            'info' => $this->getShortName(),
        ]);
    }
}
