<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\PaymentContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model implements PaymentContract
{
    use HasFactory;

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shop.models.order'));
    }
}
