<?php

namespace DV5150\Shop\Contracts\Services;

use DV5150\Shop\Contracts\Support\PaymentProviderContract;

interface ShopServiceContract
{
    public static function addPaymentProvider(string $key, PaymentProviderContract $paymentProvider): void;
    public static function getPaymentProvider(string $key);
    public static function getAllPaymentProviders(): array;
}