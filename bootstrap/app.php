<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // BU SATIRI EKLEDİK
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

     
     $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
     ]);

     // YENİ EK: Tüm web sayfalarına önbellek engelleme gardiyanını ekle.
     // Bu, 419 hatalarını kalıcı olarak önler.
     $middleware->web(append: [
        \App\Http\Middleware\PreventBackHistory::class,
     ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();