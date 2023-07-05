<?php

namespace DV5150\Shop\Contracts\Services;

interface ShopServiceContract
{
    public static function addPaymentProvider(string $key, string $paymentProvider): void;
    public static function getPaymentProvider(string $key): string;
    public static function getAllPaymentProviders(): array;
}