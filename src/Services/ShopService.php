<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\ShopServiceContract;
use DV5150\Shop\Contracts\Support\PaymentProviderContract;
use Illuminate\Support\Arr;

class ShopService implements ShopServiceContract
{
    protected static array $paymentProviders = [];

    public static function addPaymentProvider(string $key, PaymentProviderContract $paymentProvider): void
    {
        Arr::set(self::$paymentProviders, $key, $paymentProvider);
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