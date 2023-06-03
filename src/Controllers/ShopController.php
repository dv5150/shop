<?php

namespace DV5150\Shop\Controllers;

class ShopController
{
    #[Route("/order/{uuid}/thank-you", methods: ["GET"])]
    public function showThankYouPage(string $uuid)
    {
        $order = config('shop.models.order')::firstWhere('uuid', $uuid);

        abort_if(!$order, 404, __('Order not found.'));

        return view('shop::thankYou', [
            'order' => $order
        ]);
    }
}
