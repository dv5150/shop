<?php

namespace DV5150\Shop\Facades;

use DV5150\Shop\Contracts\CartServiceContract;
use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * @method static all()
     * @method static reset()
     * @method static addItem()
     * @method static removeItem()
     * @method static eraseItem()
     * @method static toArray()
     * @method static toJson()
     *
     * @see \DV5150\Shop\Services\CartService
     */
    protected static function getFacadeAccessor(): string
    {
        return CartServiceContract::class;
    }
}
