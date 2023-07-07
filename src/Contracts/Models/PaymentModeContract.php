<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PaymentModeContract
{
    public function shippingModes(): BelongsToMany;
    public function getName(): string;
    public function getProvider(): string;
    public function getPriceGross(): float;
    public function toOrderItem(): OrderItemContract;
}