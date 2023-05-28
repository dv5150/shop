<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\ShippingModeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMode extends Model implements ShippingModeContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_gross' => 'float',
        'is_active' => 'boolean',
    ];

    public function getID()
    {
        return $this->getKey();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }
}
