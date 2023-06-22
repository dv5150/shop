<?php

namespace DV5150\Shop\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShippingModeContract
{
    public function paymentModes(): BelongsToMany;
    public function getName(): string;
    public function getShortName(): string;
    public function getProvider(): string;
    public function getPriceGross(): float;
    public function getComponentName(): ?string;
    public function toOrderItem(): OrderItemContract;
}
