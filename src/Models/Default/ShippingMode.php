<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\ShippingModeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ShippingMode extends Model implements ShippingModeContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_gross' => 'float',
    ];

    public function paymentModes(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.paymentMode'));
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

    public function getComponentName(): ?string
    {
        return $this->component_name;
    }

    public function toOrderItem(): OrderItemContract
    {
        /** @var OrderItemContract $orderItem */
        $orderItem = new (config('shop.models.orderItem'))([
            'name' => $this->getName(),
            'quantity' => 1,
            'price_gross' => $this->getPriceGross(),
            'type' => Str::kebab(class_basename($this)),
        ]);

        $orderItem->sellable()->associate($this);

        return $orderItem;
    }
}
