<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\PaymentModeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentMode extends Model implements PaymentModeContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_gross' => 'float',
    ];

    public function shippingModes(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.shippingMode'));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return "[PAYMENT]";
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
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
