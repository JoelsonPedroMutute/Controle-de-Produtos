<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * Retorna resposta customizada para erros de autenticação.
     */
    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar autenticado para acessar este recurso.',
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Registrar exceções customizadas.
     */
    public function register(): void
    {
        //
    }

    /**
     * Renderiza exceções em respostas HTTP JSON personalizadas.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            // Model não encontrado
            if ($exception instanceof ModelNotFoundException) {
                $model = class_basename($exception->getModel());

                $modelNames = [
                    'User' => 'Usuário',
                    'Product' => 'Produto',
                    'Category' => 'Categoria',
                ];

                $modelName = $modelNames[$model] ?? $model;

                return response()->json([
                    'success' => false,
                    'message' => "{$modelName} não encontrado.",
                ], 404);
            }

            // Rota ou recurso não encontrado
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recurso ou endpoint não encontrado.',
                ], 404);
            }

            // Erros de validação
            if ($exception instanceof ValidationException) {
                // Detecta UUID inválido durante validação
                $errors = $exception->errors();
                foreach ($errors as $field => $messages) {
                    foreach ($messages as $key => $message) {
                        if (str_contains(strtolower($message), 'invalid input syntax for type uuid')) {
                            $errors[$field][$key] = 'O identificador informado é inválido.';
                        }
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Os dados fornecidos são inválidos.',
                    'errors' => $errors,
                ], 422);
            }

            // Erro de injeção de dependências
            if ($exception instanceof BindingResolutionException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no sistema. Verifique as dependências ou bindings.',
                ], 500);
            }

            // Erro de UUID inválido no PostgreSQL
            if ($exception instanceof QueryException &&
                str_contains(strtolower($exception->getMessage()), 'invalid input syntax for type uuid')) {
                return response()->json([
                    'success' => false,
                    'message' => 'O identificador informado é inválido.',
                    'code' => 400,
                ], 400);
            }

            // Outros erros (fallback)
            return response()->json([
                'success' => false,
                'message' => app()->environment('production')
                    ? 'Erro interno no servidor.'
                    : $exception->getMessage(),
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
