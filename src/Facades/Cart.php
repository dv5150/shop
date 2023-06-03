<?php

namespace DV5150\Shop\Facades;

use DV5150\Shop\Contracts\Services\CartServiceContract;
use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * @see \DV5150\Shop\Services\CartService
     */
    protected static function getFacadeAccessor(): string
    {
        return CartServiceContract::class;
    }
}
