<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureEmailIsVerified;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware que será executado antes dos middlewares padrão da API
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        // Alias para usar nos grupos de rotas
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'active.user' => EnsureUserIsActive::class,
            'is.admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'is.user' => \App\Http\Middleware\IsUser::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Configurações de tratamento de exceções (você pode adicionar aqui se precisar)
    })
    ->create();
