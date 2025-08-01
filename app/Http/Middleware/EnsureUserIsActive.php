<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se o usuário não estiver autenticado
        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        // Bloqueia apenas usuários NÃO admin com status diferente de 'active'
        if ($user->status !== 'active' && $user->role !== 'admin') {
            return response()->json(['message' => 'Usuário inativo. Acesso negado.'], 403);
        }

        return $next($request);
    }
}
