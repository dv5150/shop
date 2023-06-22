<?php

namespace DV5150\Shop\Tests;

use DV5150\Shop\Http\Resources\PaymentModeResource;
use DV5150\Shop\Http\Resources\ProductListResource;
use DV5150\Shop\Http\Resources\ShippingModeResource;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Models\Deals\Discount;
use DV5150\Shop\Models\Default\BillingAddress;
use DV5150\Shop\Models\Default\Order;
use DV5150\Shop\Models\Default\OrderItem;
use DV5150\Shop\Models\Default\ShippingAddress;
use DV5150\Shop\ShopServiceProvider;
use DV5150\Shop\Support\CartItemCapsule;
use DV5150\Shop\Tests\Concerns\ProvidesSampleProductData;
use DV5150\Shop\Tests\Mock\Models\Category;
use DV5150\Shop\Tests\Mock\Models\PaymentMode;
use DV5150\Shop\Tests\Mock\Models\Product;
use DV5150\Shop\Tests\Mock\Models\ShippingMode;
use DV5150\Shop\Tests\Mock\Models\User;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\HandlesRoutes;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use ProvidesSampleProductData,
        HandlesRoutes;

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
        Config::set('shop.models.coupon', Coupon::class);
        Config::set('shop.models.discount', Discount::class);
        Config::set('shop.models.order', Order::class);
        Config::set('shop.models.orderItem', OrderItem::class);
        Config::set('shop.models.paymentMode', PaymentMode::class);
        Config::set('shop.models.product', Product::class);
        Config::set('shop.models.shippingMode', ShippingMode::class);
        Config::set('shop.models.shippingAddress', ShippingAddress::class);

        /** Support tools */
        Config::set('shop.support.cartItemCapsule', CartItemCapsule::class);

        /** API Resources */
        Config::set('shop.resources.shippingMode', ShippingModeResource::class);
        Config::set('shop.resources.paymentMode', PaymentModeResource::class);
        Config::set('shop.resources.productList', ProductListResource::class);

        /** Currency setup */
        Config::set('shop.currency.code', 'HUF');

        $this->setUpSampleProductData();
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
