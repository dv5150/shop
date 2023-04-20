<?php

namespace DV5150\Shop\Controllers;

use Illuminate\Support\Facades\Redirect;

class ShopController
{
    public function showThankYouPage(string $order)
    {
        $order = config('shop.models.order')::find($order);

        if (!$order) {
            return Redirect::route(config('shop.onSuccessfulOrder.redirectRoute'));
        }

        return view('shop::thankYou.order', [
            'order' => $order
        ]);
    }
}
