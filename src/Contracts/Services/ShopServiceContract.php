<?php

namespace DV5150\Shop\Contracts\Services;

interface ShopServiceContract
{
    public static function addPaymentProvider(string $key, $paymentMethod): void;
    public static function getPaymentProvider(string $key);
    public static function getAllPaymentProviders(): array;
}