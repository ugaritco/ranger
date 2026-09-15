<?php

namespace App\Http\Middleware;

use Heritage\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => [
                'user' => $request->user(),
            ],
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
            ],
        ];
    }
}
