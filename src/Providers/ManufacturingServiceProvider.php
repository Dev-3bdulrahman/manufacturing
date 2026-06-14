<?php

namespace Dev3bdulrahman\Manufacturing\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ManufacturingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Views', 'mfg');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../Translations', 'mfg');

        // Register Livewire Components
        if (class_exists(Livewire::class)) {
            Livewire::component('mfg-work-centers', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\WorkCenters::class);
            Livewire::component('mfg-boms', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\Boms::class);
            Livewire::component('mfg-production-orders', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\ProductionOrders::class);
        }
    }
}
