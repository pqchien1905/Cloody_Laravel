<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias custom middleware
        $middleware->alias([
            'admin' => App\Http\Middleware\AdminOnly::class,
            'locale' => App\Http\Middleware\SetLocale::class,
            'rate.limit.upload' => App\Http\Middleware\RateLimitUpload::class,
        ]);

        // Add locale middleware to web group
        // Using prepend to ensure it runs after StartSession
        // StartSession is already in web group, so append will work
        $middleware->web(append: [
            App\Http\Middleware\SetLocale::class,
            App\Http\Middleware\SkipNgrokWarning::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
