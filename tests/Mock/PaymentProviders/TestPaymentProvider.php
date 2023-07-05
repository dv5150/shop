<?php

namespace DV5150\Shop\Tests\Mock\PaymentProviders;

use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Contracts\Support\PaymentProviderContract;
use Illuminate\Http\Request;

class TestPaymentProvider implements PaymentProviderContract
{
    public function pay(OrderContract $order)
    {
        //
    }

    public function callback(Request $request)
    {
        //
    }
}