<?php

namespace DV5150\Shop\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface OrderContract
{
    public function user(): BelongsTo;
    public function items(): HasMany;
}
