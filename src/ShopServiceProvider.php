<?php

namespace DV5150\Shop;

use DV5150\Shop\Console\Commands\InstallCommand;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\MessageServiceContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use DV5150\Shop\Contracts\Services\ProductListComposerServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Contracts\Transformers\OrderDataTransformerContract;
use DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract;
use DV5150\Shop\Services\CartService;
use DV5150\Shop\Services\CheckoutService;
use DV5150\Shop\Services\CouponService;
use DV5150\Shop\Services\MessageService;
use DV5150\Shop\Services\PaymentModeService;
use DV5150\Shop\Services\ProductListComposerService;
use DV5150\Shop\Services\ShippingModeService;
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

        $this->registerApiRoutes();
    }

    public function boot()
    {
        parent::boot();

        //
    }

    protected function registerBindings(): void
    {
        App::bind(OrderDataTransformerContract::class, fn () => new OrderDataTransformer());

        App::bind(OrderItemDataTransformerContract::class, fn () => new OrderItemDataTransformer());

        App::bind(CouponServiceContract::class, fn () => new CouponService());

        App::bind(ShippingModeServiceContract::class, fn () => new ShippingModeService());

        App::bind(PaymentModeServiceContract::class, fn () => new PaymentModeService());

        App::bind(MessageServiceContract::class, fn () => new MessageService());

        App::bind(CartServiceContract::class, fn () => new CartService(
            app(CouponServiceContract::class),
            app(ShippingModeServiceContract::class),
            app(PaymentModeServiceContract::class),
        ));

        App::bind(CheckoutServiceContract::class, fn () => new CheckoutService(
            app(OrderDataTransformerContract::class),
            app(OrderItemDataTransformerContract::class),
        ));

        App::bind(ProductListComposerServiceContract::class, fn () => new ProductListComposerService());
    }

    protected function registerApiRoutes(): void
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
