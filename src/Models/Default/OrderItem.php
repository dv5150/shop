<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Models\OrderItemContract;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model implements OrderItemContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
        'price_gross' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.order'));
    }

    public function sellable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getOrder(): OrderContract
    {
        return $this->order;
    }

    public function getSellable(): ?SellableItemContract
    {
        return $this->sellable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItemName(): string
    {
        return [
            'product' => $this->getName(),
            'shipping-mode' => "[Shipping Mode] {$this->getName()}",
            'payment-mode' => "[Payment Mode] {$this->getName()}",
            'coupon' => "[Coupon] {$this->getName()}",
        ][$this->getType()];
    }

    public function getInfo(): string
    {
        return $this->info;
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSubtotal(): float
    {
        return $this->getPriceGross() * $this->getQuantity();
    }

    public function getType(): string
    {
        return $this->type;
    }
}
