<?php

namespace Spork\Shopping\Http\Controller;

use Illuminate\Http\Request;
use Spork\Shopping\Services\MeijerItemService;

class ItemController
{
    public function __invoke(Request $request)
    {
        return app(MeijerItemService::class)->search($request->get('query'), $request->get('page'));
    }
}
