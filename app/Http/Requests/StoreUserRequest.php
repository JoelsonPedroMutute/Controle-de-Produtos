<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true; // Ou usar lógica baseada em políticas se preferir
    }

    /**
     * Regras de validação para criação de um novo usuário.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:user,admin',
            'status'   => 'required|in:active,inactive,pending',
            'address'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
        ];
    }
}
