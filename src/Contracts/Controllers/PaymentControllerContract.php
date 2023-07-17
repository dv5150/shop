<?php

namespace DV5150\Shop\Contracts\Controllers;

use DV5150\Shop\Contracts\Models\OrderContract;
use Illuminate\Http\Request;

interface PaymentControllerContract
{
    public function pay(string $paymentProvider, OrderContract $order);
    public function webhook(string $paymentProvider, Request $request);
}