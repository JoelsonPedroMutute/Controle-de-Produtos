<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Middleware para verificar se o usuário é um usuário comum.
 * Apenas usuários com a role 'user' podem acessar as rotas protegidas.
 */

class IsUser
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'user') {
            return response()->json([
                'message' => 'Apenas usuários comuns têm acesso a esta rota.'
            ], 403);
        }

        return $next($request);
    }
}