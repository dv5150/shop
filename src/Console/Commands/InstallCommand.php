<?php

namespace DV5150\Shop\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected array $todos = [
        'Update your migrations if necessary and run `php artisan migrate` to prepare the database.',
        'Update your `config/shop.php` config file if necessary.',
        'Update your `resources/lang/vendor/shop/en/validation.php` config file if necessary.',
    ];

    protected $signature = 'shop:install:api';

    protected $description = 'Install the webshop package API.';

    public function handle()
    {
        $this->installConfig();
        $this->installMigrations();
        $this->installTranslations();

        $this->afterHandle();

        return self::SUCCESS;
    }

    protected function installConfig(): void
    {
        $this->info('Publishing config file...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-config",
        ]);
    }

    protected function installMigrations(): void
    {
        $this->info('Publishing migrations...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-migrations",
        ]);
    }

    protected function installTranslations(): void
    {
        $this->info('Publishing translations...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-translations",
        ]);
    }

    protected function afterHandle(): void
    {
        $this->info('Installation complete.');

        for ($i = 1; $i <= $count = count($this->todos); $i++) {
            $this->warn("[$i/$count] {$this->todos[$i-1]}");
        }
    }
}
