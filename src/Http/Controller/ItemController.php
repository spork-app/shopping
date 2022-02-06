<?php

namespace Spork\Shopping\Http\Controller;

use Spork\Shopping\Services\MeijerItemService;
use Illuminate\Http\Request;

class ItemController
{
    public function __invoke(Request $request)
    {
        return app(MeijerItemService::class)->search($request->get('query'), $request->get('page'));
    }
}
