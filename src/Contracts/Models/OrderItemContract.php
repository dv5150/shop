<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface OrderItemContract
{
    public function order(): BelongsTo;
    public function sellable(): MorphTo;
    public function getOrder(): OrderContract;
    public function getSellable(): ?SellableItemContract;
    public function getPriceGross(): float;
    public function getQuantity(): int;
    public function getType(): string;
}
