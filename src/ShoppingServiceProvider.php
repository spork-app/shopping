<?php

namespace Spork\Shopping;

use App\Spork;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ShoppingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Spork::addFeature('shopping', 'ShoppingCartIcon', '/shopping');

        Route::prefix('api')->group(__DIR__ . '/../routes/api.php');
    }
}
