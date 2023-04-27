<?php

namespace DV5150\Shop\Controllers;

use Illuminate\Support\Facades\Redirect;

class ShopController
{
    public function showThankYouPage(string $uuid)
    {
        $order = config('shop.models.order')::firstWhere('uuid', $uuid);

        if (!$order) {
            return Redirect::route(config('shop.onSuccessfulOrder.redirectRoute'));
        }

        return view('shop::thankYou.order', [
            'order' => $order
        ]);
    }
}
