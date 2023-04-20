<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Concerns\Uuid;
use DV5150\Shop\Contracts\OrderContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model implements OrderContract
{
    use Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.user'));
    }

    public function items(): HasMany
    {
        return $this->hasMany(config('shop.models.orderItem'));
    }
}
