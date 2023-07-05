<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Contracts\Services\ShopServiceContract;
use DV5150\Shop\Contracts\Support\PaymentProviderContract;
use Illuminate\Support\Arr;

class ShopService implements ShopServiceContract
{
    protected static array $paymentProviders = [];

    public static function addPaymentProvider(string $key, string $paymentProvider): void
    {
        Arr::set(self::$paymentProviders, $key, self::checkPaymentProvider($paymentProvider));
    }

    public static function getPaymentProvider(string $key): string
    {
        return Arr::get(self::$paymentProviders, $key);
    }

    public static function getAllPaymentProviders(): array
    {
        return self::$paymentProviders;
    }

    protected static function checkPaymentProvider(string $paymentProvider): string
    {
        if (! in_array(PaymentProviderContract::class, class_implements($paymentProvider))) {
            throw new \Exception("Invalid payment provider.");
        }

        return $paymentProvider;
    }
}