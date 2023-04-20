<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Contracts\OrderItemContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model implements OrderItemContract
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price_gross' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.order'));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.product'));
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
