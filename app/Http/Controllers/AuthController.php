<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        // Define o role como 'user' se não for enviado
        $data['role'] = $request->input('role', 'user');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 'pending', // status inicial
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário registrado com sucesso.',
            'user' => $user,
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
            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas não são válidas.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Conta inativa. Por favor, entre em contato com o suporte.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user' => $user,
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
