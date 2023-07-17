<?php

namespace DV5150\Shop\Contracts\Support;

use DV5150\Shop\Contracts\Models\OrderContract;
use Illuminate\Http\Request;

interface PaymentProviderContract
{
    public static function getProvider(): string;
    public static function getName(): string;
    public static function isOnlinePayment(): bool;
    public function pay(OrderContract $order);
    public function webhook(Request $request);
}