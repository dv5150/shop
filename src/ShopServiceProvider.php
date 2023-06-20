<?php

namespace DV5150\Shop;

use DV5150\Shop\Console\Commands\InstallCommand;
use DV5150\Shop\Contracts\OrderDataTransformerContract;
use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Contracts\Services\CartServiceContract;
use DV5150\Shop\Contracts\Services\CheckoutServiceContract;
use DV5150\Shop\Contracts\Services\CouponServiceContract;
use DV5150\Shop\Contracts\Services\PaymentModeServiceContract;
use DV5150\Shop\Contracts\Services\ShippingModeServiceContract;
use DV5150\Shop\Models\Deals\Coupon;
use DV5150\Shop\Models\Deals\Discount;
use DV5150\Shop\Observers\DeleteCouponObserver;
use DV5150\Shop\Observers\DeleteDiscountObserver;
use DV5150\Shop\Services\CartService;
use DV5150\Shop\Services\CheckoutService;
use DV5150\Shop\Services\CouponService;
use DV5150\Shop\Services\PaymentModeService;
use DV5150\Shop\Services\ShippingModeService;
use DV5150\Shop\Transformers\OrderDataTransformer;
use DV5150\Shop\Transformers\OrderItemDataTransformer;
use DV5150\Shop\View\Composers\ProductListComposer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ShopServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();

        $this->registerApiRoutes();

        $this->commands([
            InstallCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mountObservers();

        $this->loadViewsFrom($this->getPath('resources/views'), 'shop');

        $this->loadTranslationsFrom($this->getPath('resources/lang'), 'shop');

        View::composer('shop::partials.productList', ProductListComposer::class);
    }

    protected function registerBindings(): void
    {
        App::bind(OrderDataTransformerContract::class, fn () => new OrderDataTransformer());

        App::bind(OrderItemDataTransformerContract::class, fn () => new OrderItemDataTransformer());

        App::bind(CouponServiceContract::class, fn () => new CouponService());

        App::bind(ShippingModeServiceContract::class, fn () => new ShippingModeService());

        App::bind(PaymentModeServiceContract::class, fn () => new PaymentModeService());

        App::bind(CartServiceContract::class, fn () => new CartService(
            app(CouponServiceContract::class),
            app(ShippingModeServiceContract::class),
            app(PaymentModeServiceContract::class),
        ));

        App::bind(CheckoutServiceContract::class, fn () => new CheckoutService(
            app(OrderDataTransformerContract::class),
            app(OrderItemDataTransformerContract::class),
        ));
    }

    protected function registerApiRoutes(): void
    {
        Route::middleware('web')
            ->prefix('api/shop')
            ->as('api.shop.')
            ->group($this->getPath('routes/shop-api.php'));

        Route::middleware('web')
            ->as('shop.')
            ->group($this->getPath('routes/shop.php'));
    }

    protected function mountObservers(): void
    {
        Discount::observe(DeleteDiscountObserver::class);
        Coupon::observe(DeleteCouponObserver::class);
    }

    protected function getPath(?string $target = null): string
    {
        return __DIR__ . "/../$target";
    }
}
