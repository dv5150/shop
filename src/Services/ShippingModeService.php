<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class ShippingModeService implements ShippingModeServiceContract
{
    protected const SESSION_KEY = 'shippingMode';

    public function getShippingMode(): ?ShippingModeContract
    {
        if ($shippingMode = Session::get(self::SESSION_KEY)) {
            $shippingMode = $this->unserializeShippingMode($shippingMode);
            $this->setShippingMode($shippingMode);
        }

        return $shippingMode;
    }

    public function setShippingMode(?ShippingModeContract $shippingMode): void
    {
        /** @var Model $shippingMode */

        Session::put(
            self::SESSION_KEY,
            $shippingMode?->exists() ? serialize($shippingMode->refresh()) : null
        );
    }

    protected function unserializeShippingMode(string $serializedShippingMode): ?ShippingModeContract
    {
        $shippingMode = unserialize($serializedShippingMode);

        return $shippingMode->exists() ? $shippingMode->refresh() : null;
    }
}
