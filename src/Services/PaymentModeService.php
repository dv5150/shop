<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Models\PaymentModeContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class PaymentModeService implements PaymentModeServiceContract
{
    protected const SESSION_KEY = 'paymentMode';

    public function getPaymentMode(): ?PaymentModeContract
    {
        if ($paymentMode = Session::get(self::SESSION_KEY)) {
            $paymentMode = $this->unserializePaymentMode($paymentMode);
            $this->setPaymentMode($paymentMode);
        }

        return $paymentMode;
    }

    public function setPaymentMode(?PaymentModeContract $paymentMode): void
    {
        /** @var Model $paymentMode */

        Session::put(
            self::SESSION_KEY,
            $paymentMode?->exists() ? serialize($paymentMode->refresh()) : null
        );
    }

    protected function unserializePaymentMode(string $serializedPaymentMode): ?PaymentModeContract
    {
        $paymentMode = unserialize($serializedPaymentMode);

        return $paymentMode->exists() ? $paymentMode->refresh() : null;
    }
}
