<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;



class AuthController extends Controller
{
    /**
     * Realiza o registro de um novo usuário.
     */
   public function register(RegisterUserRequest $request)
{
    $data = $request->validated();

    // Verifica se o e-mail já está em uso
    if (User::where('email', $data['email'])->exists()) {
        return response()->json([
            'message' => 'O e-mail já está em uso.',
        ], 422);
    }

    $isFirstAdmin = User::where('role', 'admin')->count() === 0;

    // Se for admin e for o primeiro, já ativa. Senão, pendente.
    $status = ($data['role'] === 'admin' && $isFirstAdmin)
        ? 'active'
        : 'pending';

    $user = User::create([
        'id' => Str::uuid(),
        'name' => $data['name'],
        'email' => $data['email'],
        'password'=> Hash::make($data['password']),
        'role' => $data['role'],
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'status' => $status,
    ]);

    $token = $user->status === 'active'
        ? $user->createToken('auth_token')->plainTextToken
        : null;

    return response()->json([
        'message' => 'Usuário registrado com sucesso.',
        'user' => new UserResource($user),
        'token' => $token,
    ], 201);
}

    /**
     * Realiza o login do usuário com e-mail e senha.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

      if (! $user || ! Hash::check($request->password, $user->password)) {
    return response()->json([
        'message' => 'As credenciais informadas não são válidas.'
    ], 401);
}


        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Conta inativa. Por favor, entre em contato com o suporte.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    /**
     * Faz o logout do usuário autenticado.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Usuário não autenticado.',
            ], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ], 200);
    }
}
