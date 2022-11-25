<?php

namespace Spork\Shopping;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spork\Core\Spork;

class \ShoppingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Spork::addFeature('shopping', 'ShoppingCartIcon', '/shopping', 'tool');
        $this->mergeConfigFrom(__DIR__ . '/../config/spork.php', 'spork.shopping');

        if (config('spork.shopping.enabled')) {
            Route::prefix('api')->group(__DIR__.'/../routes/api.php');
        }
    }
}
