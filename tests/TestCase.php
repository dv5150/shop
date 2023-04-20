<?php

namespace DV5150\Shop\Tests;

use DV5150\Shop\ShopServiceProvider;
use DV5150\Shop\Tests\Mock\Models\Order;
use DV5150\Shop\Tests\Mock\Models\OrderItem;
use DV5150\Shop\Tests\Mock\Models\Product;
use DV5150\Shop\Tests\Mock\Models\User;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\HandlesRoutes;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use HandlesRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();

        Config::set('shop.models.product', Product::class);
        Config::set('shop.models.order', Order::class);
        Config::set('shop.models.orderItem', OrderItem::class);
        Config::set('shop.models.user', User::class);

        Config::set('shop.onSuccessfulOrder.redirectRoute', 'home');
    }

    protected function getPackageProviders($app)
    {
        return [
            ShopServiceProvider::class,
        ];
    }

    protected function defineRoutes($router)
    {
        $router->get('home', fn () => 'Welcome')
            ->name('home');
    }
}
