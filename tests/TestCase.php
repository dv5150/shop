<?php

namespace DV5150\Shop\Tests;

use DV5150\Shop\Contracts\Controllers\PaymentControllerContract;
use DV5150\Shop\Contracts\Models\OrderContract;
use DV5150\Shop\Http\Resources\CategoryResource;
use DV5150\Shop\Http\Resources\PaymentModeResource;
use DV5150\Shop\Http\Resources\ProductResource;
use DV5150\Shop\Http\Resources\ShippingAddressResource;
use DV5150\Shop\Http\Resources\ShippingModeResource;
use DV5150\Shop\Models\Default\BillingAddress;
use DV5150\Shop\Models\Default\Order;
use DV5150\Shop\Models\Default\OrderItem;
use DV5150\Shop\Models\Default\Payment;
use DV5150\Shop\Models\Default\ShippingAddress;
use DV5150\Shop\ShopServiceProvider;
use DV5150\Shop\Support\ShopItemCapsule;
use DV5150\Shop\Tests\Mock\Models\Category;
use DV5150\Shop\Tests\Mock\Models\Deals\Coupon;
use DV5150\Shop\Tests\Mock\Models\Deals\Discount;
use DV5150\Shop\Tests\Mock\Models\PaymentMode;
use DV5150\Shop\Tests\Mock\Models\Product;
use DV5150\Shop\Tests\Mock\Models\ShippingMode;
use DV5150\Shop\Tests\Mock\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\HandlesRoutes;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use HandlesRoutes, InteractsWithContainer;

    protected string $productClass;
    protected string $categoryClass;

    protected string $shopItemCapsuleClass;

    protected array $sampleAddress = [
        'display_name' => 'Test address',
        'name' => 'Johnny',
        'zip_code' => '1234',
        'city' => 'Budapest',
        'address' => 'Sample street 2',
        'phone' => '+36301234567',
        'comment' => 'Some comment goes here',
    ];

    protected array $testOrderDataRequired = [
        'personalData' => [
            'email' => 'tester+mailaddress+10000@my-webshop.com',
            'phone' => '+36301001000',
        ],
        'shippingData' => [
            'name' => 'Test Name 1000',
            'zipCode' => '1000',
            'city' => 'Budapest 1000',
            'street' => 'One street 1000',
        ],
        'billingData' => [
            'name' => 'Another Name 9000',
            'zipCode' => '9000',
            'city' => 'Győr 9000',
            'street' => 'Street 9000',
        ],
    ];

    protected array $expectedOrderDataRequired = [
        'email' => 'tester+mailaddress+10000@my-webshop.com',
        'phone' => '+36301001000',
        'shipping_name' => 'Test Name 1000',
        'shipping_zip_code' => '1000',
        'shipping_city' => 'Budapest 1000',
        'shipping_address' => 'One street 1000',
        'billing_name' => 'Another Name 9000',
        'billing_zip_code' => '9000',
        'billing_city' => 'Győr 9000',
        'billing_address' => 'Street 9000',
    ];

    protected function setUp(): void
    {
        parent::setUp();

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
        Config::set('shop.models.payment', Payment::class);
        Config::set('shop.models.paymentMode', PaymentMode::class);
        Config::set('shop.models.product', Product::class);
        Config::set('shop.models.shippingMode', ShippingMode::class);
        Config::set('shop.models.shippingAddress', ShippingAddress::class);

        /** Support tools */
        Config::set('shop.support.shopItemCapsule', ShopItemCapsule::class);

        /** API Resources */
        Config::set('shop.resources.shippingMode', ShippingModeResource::class);
        Config::set('shop.resources.paymentMode', PaymentModeResource::class);
        Config::set('shop.resources.product', ProductResource::class);
        Config::set('shop.resources.shippingAddress', ShippingAddressResource::class);
        Config::set('shop.resources.category', CategoryResource::class);

        /** Currency setup */
        Config::set('shop.currency.code', 'HUF');

        $this->productClass = config('shop.models.product');
        $this->categoryClass = config('shop.models.category');
        $this->shopItemCapsuleClass = config('shop.support.shopItemCapsule');
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

        $router->get('order/{order:uuid}/thank-you', fn (OrderContract $order) => $order->getUuid())
            ->name('shop.order.thankYou');

        $router->get('payment/{paymentProvider}/pay/{order:uuid}', [app(PaymentControllerContract::class), 'pay'])
            ->name('shop.pay');
    }
}
