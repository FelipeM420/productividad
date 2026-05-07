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
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar aliases para usar en rutas
        $middleware->alias([
            'solo.admin'    => \App\Http\Middleware\SoloAdmin::class,
            'solo.vendedor' => \App\Http\Middleware\SoloVendedor::class,
            'solo.auditor'  => \App\Http\Middleware\SoloAuditor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();