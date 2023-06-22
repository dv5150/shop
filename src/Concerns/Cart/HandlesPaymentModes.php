<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Support\CartCollection;

trait HandlesPaymentModes
{
    public function setPaymentMode(?PaymentModeContract $paymentMode): CartCollection
    {
        $this->paymentModeService->setPaymentMode($paymentMode);

        return $this->all();
    }

    public function getPaymentMode(): ?PaymentModeContract
    {
        return $this->paymentModeService->getPaymentMode();
    }
}