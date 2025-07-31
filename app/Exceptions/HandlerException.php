<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;

class Handler extends ExceptionHandler
{
    
    public function unauthenticated($request, AuthenticationException $exception)
{
    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Você precisa estar autenticado para acessar este recurso.',
        ], 401);
    }

    return redirect()->guest(route('login'));
}


    // ✅ Método render personalizado
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof BindingResolutionException) {
            return response()->json([
                'message' => 'Erro interno no sistema. Verifique as dependências ou bindings.',
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
