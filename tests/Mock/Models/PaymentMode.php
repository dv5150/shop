<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Models\Default\PaymentMode as ShopPaymentMode;
use DV5150\Shop\Tests\Mock\Factories\PaymentModeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMode extends ShopPaymentMode
{
    public static function newFactory(): Factory
    {
        return PaymentModeFactory::new();
    }
}
