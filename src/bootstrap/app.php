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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'local';

switch ($env) {
    case 'production':
        $app->loadEnvironmentFrom('.env.production');
        break;
    default:
        $app->loadEnvironmentFrom('.env');
        break;
}
return $app;