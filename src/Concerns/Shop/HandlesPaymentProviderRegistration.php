<?php

namespace DV5150\Shop\Concerns\Shop;

use DV5150\Shop\Contracts\Support\PaymentProviderContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HandlesPaymentProviderRegistration
{
    protected static array $paymentProviders = [];

    public static function registerPaymentProviders(array $paymentProviders): void
    {
        self::filterPaymentProviders(collect($paymentProviders))
            ->each(fn (string $provider) => Arr::set(self::$paymentProviders, $provider::getProvider(), $provider));
    }

    public static function getPaymentProvider(string $key): ?string
    {
        return Arr::get(self::getAllPaymentProviders(), $key);
    }

    public static function hasPaymentProvider(string $key): bool
    {
        return Arr::has(self::getAllPaymentProviders(), $key);
    }

    public static function getAllPaymentProviders(): array
    {
        return self::filterPaymentProviders(collect(self::$paymentProviders))->all();
    }

    public static function filterPaymentProviders(Collection $paymentProviders): Collection
    {
        return $paymentProviders->filter(fn (string $provider) => self::checkPaymentProvider($provider));
    }

    protected static function checkPaymentProvider(string $paymentProvider): bool
    {
        return in_array(PaymentProviderContract::class, class_implements($paymentProvider));
    }
}