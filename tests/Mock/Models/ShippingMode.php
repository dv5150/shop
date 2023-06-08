<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Models\Default\ShippingMode as ShopShippingMode;
use DV5150\Shop\Tests\Mock\Factories\ShippingModeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingMode extends ShopShippingMode
{
    public static function newFactory(): Factory
    {
        return ShippingModeFactory::new();
    }
}
