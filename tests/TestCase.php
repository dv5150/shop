<?php

namespace DV5150\Shop\Tests;

use DV5150\Shop\Models\Default\BillingAddress;
use DV5150\Shop\Models\Default\Category;
use DV5150\Shop\Models\Default\Order;
use DV5150\Shop\Models\Default\OrderItem;
use DV5150\Shop\Models\Default\PaymentMode;
use DV5150\Shop\Models\Default\ShippingAddress;
use DV5150\Shop\Models\Default\ShippingMode;
use DV5150\Shop\Tests\Mock\Models\Product;
use DV5150\Shop\Tests\Mock\Models\User;
use DV5150\Shop\ShopServiceProvider;
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

        $this->loadMigrationsFrom('database/migrations');

        /** Mock models */
        Config::set('shop.models.user', User::class);

        /** Real models or extends real models */
        Config::set('shop.models.billingAddress', BillingAddress::class);
        Config::set('shop.models.category', Category::class);
        Config::set('shop.models.order', Order::class);
        Config::set('shop.models.orderItem', OrderItem::class);
        Config::set('shop.models.paymentMode', PaymentMode::class);
        Config::set('shop.models.product', Product::class);
        Config::set('shop.models.shippingMode', ShippingMode::class);
        Config::set('shop.models.shippingAddress', ShippingAddress::class);

        /** Currency setup */
        Config::set('shop.currency.code', 'HUF');

        /** Default shipping mode settings */
        Config::set('shop.defaultShippingMode.name', 'TEST SHIPPING MODE');
        Config::set('shop.defaultShippingMode.priceGross', 490.0);
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
