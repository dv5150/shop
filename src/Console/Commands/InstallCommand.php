<?php

namespace DV5150\Shop\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected array $migrations = [
        '01_create_billing_addresses_table',
        '02_create_shipping_addresses_table',
        '03_create_products_table',
        '04_create_categories_table',
        '05_create_orders_table',
        '06_create_order_items_table',
        '07_create_category_product_table',
        '08_create_shipping_modes_table',
        '09_create_payment_modes_table',
    ];

    protected array $models = [
        'BillingAddress',
        'Category',
        'Order',
        'OrderItem',
        'PaymentMode',
        'Product',
        'ShippingAddress',
        'ShippingMode',
    ];

    protected array $filamentResources = [
        'OrderResource/Pages/CreateOrder',
        'OrderResource/Pages/EditOrder',
        'OrderResource/Pages/ListOrders',
        'OrderResource/RelationManagers/ItemsRelationManager',
        'ProductResource/Pages/CreateProduct',
        'ProductResource/Pages/EditProduct',
        'ProductResource/Pages/ListProducts',
        'UserResource/RelationManagers/OrdersRelationManager',
        'OrderResource',
        'ProductResource',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the webshop package.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->runInstallationProcess();

        $this->info('Installation complete. Update your `shop.php` config file and run `php artisan migrate`');

        return self::SUCCESS;
    }

    protected function runInstallationProcess(): void
    {
        $this->copyMigrations();
        $this->copyConfigFile();

        if ($this->confirm('Copy default models? (recommended)', true)) {
            $this->copyDefaultModels();
        }

        if ($this->confirm('Copy Vue components and views? (recommended)', true)) {
            $this->copyFrontendAssets();
        }

        if ($this->confirm('Copy Filament resources?', false)) {
            $this->copyFilamentResources();
        }
    }

    protected function copyMigrations(): void
    {
        $this->info('Copying database migrations...');

        $date = now()->format('Y_m_d');

        foreach ($this->migrations as $migration) {
            File::copy(
                $this->getPath("database/migrations/$migration.php.stub"),
                database_path("migrations/{$date}_shop_{$migration}.php")
            );
        }
    }

    protected function copyConfigFile(): void
    {
        $this->info('Copying config file...');

        File::copy($this->getPath('config/shop.php'), config_path('shop.php'));
    }

    protected function copyDefaultModels(): void
    {
        $this->info('Copying default models...');

        File::ensureDirectoryExists(app_path('Models/Shop'));

        foreach ($this->models as $model) {
            File::copy(
                $this->getPath("src/Models/Shop/$model.php.stub"),
                app_path("Models/Shop/$model.php")
            );
        }
    }

    protected function copyFrontendAssets(): void
    {
        $this->info('Copying frontend assets...');

        File::ensureDirectoryExists(resource_path('js/components/shop'));
        File::ensureDirectoryExists(resource_path('views/vendor/shop'));

        File::copyDirectory(
            $this->getPath('resources/js/components'),
            resource_path('js/components/shop')
        );

        File::copyDirectory(
            $this->getPath('resources/views'),
            resource_path('views/vendor/shop')
        );
    }

    protected function copyFilamentResources(): void
    {
        $this->info('Copying Filament resources...');

        $directories = [
            'Filament/Resources/OrderResource/Pages',
            'Filament/Resources/OrderResource/RelationManagers',
            'Filament/Resources/ProductResource/Pages',
            'Filament/Resources/UserResource/RelationManagers',
        ];

        foreach ($directories as $directory) {
            File::ensureDirectoryExists(app_path($directory));
        }

        foreach ($this->filamentResources as $resource) {
            File::copy(
                $this->getPath("src/Filament/Resources/$resource.php.stub"),
                app_path("Filament/Resources/$resource.php")
            );
        }
    }

    private function getPath(string $path): string
    {
        return __DIR__ . "/../../../$path";
    }
}