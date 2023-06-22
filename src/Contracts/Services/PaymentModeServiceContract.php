<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Models\PaymentModeContract;

interface PaymentModeServiceContract
{
    public function setPaymentMode(?PaymentModeContract $paymentMode): void;
    public function getPaymentMode(): ?PaymentModeContract;
}
