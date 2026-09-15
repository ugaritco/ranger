<?php

use Heritage\Foundation\Application;
use Heritage\Foundation\Configuration\Exceptions;
use Heritage\Foundation\Configuration\Middleware;

use function Orchestra\Testbench\default_skeleton_path;

return Application::configure(basePath: $APP_BASE_PATH ?? default_skeleton_path())
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
