<?php

namespace DV5150\Shop\Concerns\Cart;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;

trait HandlesPaymentModes
{
    public function setPaymentMode(?PaymentModeContract $paymentMode): CartCollectionContract
    {
        $this->paymentModeService->setPaymentMode($paymentMode);

        return $this->all();
    }

    public function getPaymentMode(): ?PaymentModeContract
    {
        return $this->paymentModeService->getPaymentMode();
    }
}