<?php

namespace DV5150\Shop\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'shop:install:api';

    protected $description = 'Install the webshop package API.';

    public function handle()
    {
        $this->info('Publishing config file...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-config",
        ]);

        $this->info('Publishing migrations...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-migrations",
        ]);

        $this->info('Publishing translations...');

        $this->callSilently("vendor:publish", [
            '--tag' => "shop-translations",
        ]);

        $this->info('Installation complete. Run `php artisan migrate` to create the required tables.');

        return self::SUCCESS;
    }
}
