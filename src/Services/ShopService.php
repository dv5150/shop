<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Concerns\Shop\HandlesPaymentProviderRegistration;
use DV5150\Shop\Contracts\Services\ShopServiceContract;

class ShopService implements ShopServiceContract
{
    use HandlesPaymentProviderRegistration;

    public static function isFrontendInstalled(): bool
    {
        return class_exists('DV5150\\Shop\\Frontend\\ShopFrontendServiceProvider');
    }
}