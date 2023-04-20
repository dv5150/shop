<?php

namespace DV5150\Shop\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface OrderItemContract
{
    public function order(): BelongsTo;
    public function product(): BelongsTo;
    public function getPriceGross(): float;
    public function getQuantity(): int;
}
