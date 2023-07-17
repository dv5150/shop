<?php

namespace DV5150\Shop\Tests\Mock\PaymentProviders;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Support\PaymentProviderContract;
use Illuminate\Http\Request;

class TestPaymentProvider implements PaymentProviderContract
{
    public static function getProvider(): string
    {
        return 'test';
    }

    public static function getName(): string
    {
        return 'Test';
    }

    public static function isOnlinePayment(): bool
    {
        return false;
    }

    public function pay(OrderContract $order)
    {
        //
    }

    public function webhook(Request $request)
    {
        //
    }
}