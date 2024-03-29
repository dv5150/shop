<?php

namespace DV5150\Shop\Facades;

use DV5150\Shop\Contracts\Services\ShopServiceContract;
use DV5150\Shop\Services\ShopService;
use Illuminate\Support\Facades\Facade;

class Shop extends Facade
{
    /**
     * @see ShopService
     */
    protected static function getFacadeAccessor(): string
    {
        return ShopServiceContract::class;
    }
}
