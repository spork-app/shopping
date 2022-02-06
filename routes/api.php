<?php

Route::middleware(['api', 'auth:sanctum'])->get('shopping-search', \Spork\Shopping\Http\Controller\ItemController::class);
