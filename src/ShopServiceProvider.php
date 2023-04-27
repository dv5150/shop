<?php

namespace DV5150\Shop;

use DV5150\Shop\Contracts\CartServiceContract;
use DV5150\Shop\Contracts\OrderDataTransformerContract;
use DV5150\Shop\Contracts\OrderItemDataTransformerContract;
use DV5150\Shop\Services\CartService;
use DV5150\Shop\Transformers\OrderDataTransformer;
use DV5150\Shop\Transformers\OrderItemDataTransformer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
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
        $this->registerCartService();
        $this->registerTransformers();
        $this->registerApiRoutes();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom($this->getPath('resources/views'), 'shop');

        app()->runningUnitTests()
            ? $this->bootTesting()
            : $this->bootDefault();
    }

    protected function bootDefault(): void
    {
        $this->publishes([
            $this->getPath('config/shop.php') => config_path('shop.php'),
            $this->getPath('database/migrations') => database_path('migrations'),
            $this->getPath('resources/views') => resource_path('views/vendor/shop'),
        ], 'shop');
    }

    protected function bootTesting(): void
    {
        $this->loadMigrationsFrom($this->getPath('database/migrations'));
    }

    protected function registerCartService(): void
    {
        App::bind(
            CartServiceContract::class,
            fn () => new CartService()
        );
    }

    protected function registerTransformers(): void
    {
        App::bind(
            OrderDataTransformerContract::class,
            fn () => new OrderDataTransformer()
        );

        App::bind(
            OrderItemDataTransformerContract::class,
            fn () => new OrderItemDataTransformer()
        );
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

    protected function getPath(?string $target = null): string
    {
        return __DIR__ . "/../$target";
    }
}
