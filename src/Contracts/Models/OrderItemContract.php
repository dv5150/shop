<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface OrderItemContract
{
    public function order(): BelongsTo;
    public function product(): BelongsTo;
    public function getOrder(): OrderContract;
    public function getProduct(): ?ProductContract;
    public function getPriceGross(): float;
    public function getQuantity(): int;
}
