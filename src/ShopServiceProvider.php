<?php

namespace DV5150\Shop;

use DV5150\Shop\Console\Commands\InstallCommand;
use DV5150\Shop\Contracts\Controllers\API\CartAPIControllerContract;
use DV5150\Shop\Contracts\Controllers\API\CheckoutAPIControllerContract;
use DV5150\Shop\Contracts\Controllers\PaymentControllerContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\MessageServiceContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use DV5150\Shop\Contracts\Services\ProductListComposerServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\Services\ShopServiceContract;
use DV5150\Shop\Contracts\Support\CartCollectionContract;
use DV5150\Shop\Contracts\Transformers\OrderDataTransformerContract;
use DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract;
use DV5150\Shop\Http\Controllers\API\CartAPIController;
use DV5150\Shop\Http\Controllers\API\CheckoutAPIController;
use DV5150\Shop\Http\Controllers\PaymentController;
use DV5150\Shop\Services\CartService;
use DV5150\Shop\Services\CheckoutService;
use DV5150\Shop\Services\CouponService;
use DV5150\Shop\Services\MessageService;
use DV5150\Shop\Services\PaymentModeService;
use DV5150\Shop\Services\ProductListComposerService;
use DV5150\Shop\Services\ShippingModeService;
use DV5150\Shop\Services\ShopService;
use DV5150\Shop\Support\CartCollection;
use DV5150\Shop\Transformers\OrderDataTransformer;
use DV5150\Shop\Transformers\OrderItemDataTransformer;
use DV5150\Shop\View\Composers\ProductListComposer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShopServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('shop')
            ->hasConfigFile('shop')
            ->hasTranslations()
            ->hasMigrations([
                '01_create_billing_addresses_table',
                '02_create_shipping_addresses_table',
                '03_create_products_table',
                '04_create_categories_table',
                '05_create_category_product_table',
                '06_create_shipping_modes_table',
                '07_create_payment_modes_table',
                '08_create_payment_mode_shipping_mode_table',
                '09_create_orders_table',
                '10_create_order_items_table',
                '11_create_discount_tables',
                '12_create_coupon_tables',
                '13_create_payments_table',
            ])
            ->hasViewComposer(
                'shop::partials.productList',
                ProductListComposer::class,
            )
            ->hasCommand(InstallCommand::class);
    }

    public function register()
    {
        parent::register();

        $this->registerBindings();

        $this->registerShopRoutes();
        $this->registerShopApiRoutes();
    }

    public function boot()
    {
        parent::boot();

        Route::bind('order', function (string $uuid) {
            return config('shop.models.order')::whereUuid($uuid)->firstOrFail();
        });
    }

    protected function registerBindings(): void
    {
        App::bind(ShopServiceContract::class, fn () => new ShopService());

        App::bind(OrderDataTransformerContract::class, fn () => new OrderDataTransformer());

        App::bind(OrderItemDataTransformerContract::class, fn () => new OrderItemDataTransformer());

        App::bind(CouponServiceContract::class, fn () => new CouponService());

        App::bind(ShippingModeServiceContract::class, fn () => new ShippingModeService());

        App::bind(PaymentModeServiceContract::class, fn () => new PaymentModeService());

        App::bind(MessageServiceContract::class, fn () => new MessageService());

        App::bind(CartCollectionContract::class, fn () => new CartCollection());

        App::bind(CartServiceContract::class, fn () => new CartService(
            app(CouponServiceContract::class),
            app(ShippingModeServiceContract::class),
            app(PaymentModeServiceContract::class),
        ));

        App::bind(CheckoutServiceContract::class, fn () => new CheckoutService(
            app(OrderDataTransformerContract::class),
        ));

        App::bind(ProductListComposerServiceContract::class, fn () => new ProductListComposerService());

        App::bind(CartAPIControllerContract::class, fn () => CartAPIController::class);

        App::bind(CheckoutAPIControllerContract::class, fn () => CheckoutAPIController::class);

        App::bind(PaymentControllerContract::class, fn () => PaymentController::class);
    }

    protected function registerShopRoutes(): void
    {
        Route::middleware('web')
            ->as('shop.')
            ->group($this->getPath('routes/shop.php'));
    }

    protected function registerShopApiRoutes(): void
    {
        Route::middleware('web')
            ->prefix('api/shop')
            ->as('api.shop.')
            ->group($this->getPath('routes/shop-api.php'));
    }

    protected function getPath(?string $target = null): string
    {
        return __DIR__ . "/../$target";
    }
}
