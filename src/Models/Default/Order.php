<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\OrderContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model implements OrderContract
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.user'));
    }

    public function items(): HasMany
    {
        return $this->hasMany(config('shop.models.orderItem'));
    }

    public function shippingMode(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.shippingMode'));
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.paymentMode'));
    }

    public function getThankYouUrl(): string
    {
        return route('shop.order.thankYou', [
            'uuid' => $this->uuid
        ]);
    }
}
