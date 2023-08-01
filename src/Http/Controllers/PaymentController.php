<?php

namespace DV5150\Shop\Http\Controllers;

use DV5150\Shop\Contracts\Controllers\PaymentControllerContract;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Facades\Shop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class PaymentController extends Controller implements PaymentControllerContract
{
    public function pay(string $paymentProvider, OrderContract $order)
    {
        if ($order->isPaid()) {
            return redirect()->home()->withErrors([
                'paymentError' => __("This order has been already paid.")
            ]);
        }

        if (! $this->providerExists($paymentProvider)) {
            return redirect()->home()->withErrors([
                'paymentError' => __("Payment provider does not exist.")
            ]);
        }

        $order->load('items.sellable');

        return (new (Shop::getPaymentProvider($paymentProvider)))->pay($order);
    }

    public function webhook(string $paymentProvider, Request $request)
    {
        if (! $this->providerExists($paymentProvider)) {
            return redirect()->home()->withErrors([
                'paymentError' => __("Payment provider does not exist.")
            ]);
        }

        return (new (Shop::getPaymentProvider($paymentProvider)))->webhook($request);
    }

    protected function providerExists(string $paymentProvider): bool
    {
        return Arr::has(Shop::getAllPaymentProviders(), $paymentProvider);
    }
}
