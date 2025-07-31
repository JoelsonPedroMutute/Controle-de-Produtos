<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está autenticado
        if (Auth::check() && Auth::user()->status !== 'active') {
            return response()->json(['message' => 'Usuário inativo.'], 403);
        }

        return $next($request);
    }
}
