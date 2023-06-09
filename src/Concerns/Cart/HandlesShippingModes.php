<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Contracts\Models\ShippingModeContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;

trait HandlesShippingModes
{
    public function setShippingMode(?ShippingModeContract $shippingMode): CartCollectionContract
    {
        $this->shippingModeService->setShippingMode($shippingMode);
        $this->paymentModeService->setPaymentMode(null);

        return $this->all();
    }

    public function getShippingMode(): ?ShippingModeContract
    {
        return $this->shippingModeService->getShippingMode();
    }
}