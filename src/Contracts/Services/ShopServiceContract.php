<?php

namespace DV5150\Shop\Contracts\Services;

interface ShopServiceContract
{
    public static function registerPaymentProviders(array $paymentProviders): void;
    public static function getPaymentProvider(string $key): ?string;
    public static function getAllPaymentProviders(): array;
    public static function isFrontendInstalled(): bool;
}