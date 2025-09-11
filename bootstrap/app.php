<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Register custom middleware aliases
        $middleware->alias([
            'validate.file.upload' => \App\Http\Middleware\ValidateFileUpload::class,
            'check.import.export.permission' => \App\Http\Middleware\CheckImportExportPermission::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'validate.csrf' => \App\Http\Middleware\ValidateCSRF::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
