<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface PaymentContract
{
    public function order(): BelongsTo;
}