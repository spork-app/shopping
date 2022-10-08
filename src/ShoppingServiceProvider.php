<?php

namespace Spork\Shopping;

use Spork\Core\Spork;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ShoppingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Spork::addFeature('shopping', 'ShoppingCartIcon', '/shopping', 'tool');

        if (config('spork.shopping.enabled')) {
            Route::prefix('api')->group(__DIR__ . '/../routes/api.php');
        }
    }
}
