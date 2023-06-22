<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Models\ShippingModeContract;

interface ShippingModeServiceContract
{
    public function setShippingMode(?ShippingModeContract $shippingMode): void;
    public function getShippingMode(): ?ShippingModeContract;
}
