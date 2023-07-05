<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\ShopServiceContract;
use Illuminate\Support\Arr;

class ShopService implements ShopServiceContract
{
    protected static array $paymentProviders = [];

    public static function addPaymentProvider(string $key, $paymentMethod): void
    {
        Arr::set(self::$paymentProviders, $key, $paymentMethod);
    }

    public static function getPaymentProvider(string $key)
    {
        return Arr::get(self::$paymentProviders, $key);
    }

    public static function getAllPaymentProviders(): array
    {
        return self::$paymentProviders;
    }
}