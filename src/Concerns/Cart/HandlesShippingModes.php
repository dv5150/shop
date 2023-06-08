<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Contracts\ShippingModeContract;
use DV5150\Shop\Support\CartCollection;

trait HandlesShippingModes
{
    public function setShippingMode(?ShippingModeContract $shippingMode): CartCollection
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