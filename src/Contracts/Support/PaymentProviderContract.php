<?php

namespace DV5150\Shop\Contracts\Support;

use DV5150\Shop\Contracts\Models\OrderContract;
use Illuminate\Http\Request;

interface PaymentProviderContract
{
    public function pay(OrderContract $order);
    public function callback(Request $request);
}