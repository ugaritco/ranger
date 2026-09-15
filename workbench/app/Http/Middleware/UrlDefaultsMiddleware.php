<?php

namespace App\Http\Middleware;

use Heritage\Support\Facades\URL;

class UrlDefaultsMiddleware
{
    public function handle($request, $next)
    {
        URL::defaults([
            'locale' => 'en',
        ]);

        return $next($request);
    }
}
