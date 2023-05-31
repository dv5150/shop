<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\ShippingModeContract;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class ShippingModeService implements ShippingModeServiceContract
{
    public function getShippingMode(): ?ShippingModeContract
    {
        if ($shippingMode = Session::get($this->getSessionKey())) {
            $shippingMode = $this->unserializeShippingMode($shippingMode);
            $this->setShippingMode($shippingMode);
        } else {
            $shippingMode = $this->getDefaultShippingMode();
            $this->setShippingMode($shippingMode);
        }

        return $shippingMode;
    }

    public function setShippingMode(ShippingModeContract $shippingMode): void
    {
        /** @var Model $shippingMode */

        Session::put(
            $this->getSessionKey(),
            $shippingMode?->exists() ? serialize($shippingMode) : null
        );
    }

    protected function unserializeShippingMode(string $serializedShippingMode): ShippingModeContract
    {
        $shippingMode = unserialize($serializedShippingMode);

        return $shippingMode->exists()
            ? $shippingMode
            : $this->getDefaultShippingMode();
    }

    protected function getDefaultShippingMode(): ShippingModeContract
    {
        return config('shop.models.shippingMode')::firstOrNew([
            'provider' => 'default',
        ], [
            'name' => config('shop.defaultShippingMode.name'),
            'price_gross' => config('shop.defaultShippingMode.priceGross'),
        ]);
    }

    protected function getSessionKey(): string
    {
        return 'shippingMode';
    }
}
