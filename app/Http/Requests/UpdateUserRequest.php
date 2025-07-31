<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // <- import correto

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aqui você pode usar lógica de autorização se quiser restringir esse update
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    $authUser = Auth::user();
    $userId   = $this->route('user'); // pode ser nulo se for /me

    $rules = [
        'name'    => 'required|string|max:255',
        'email'   => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($userId ?? $authUser->id),
        ],
        'address' => 'nullable|string|max:255',
        'phone'   => 'nullable|string|max:20',
    ];

    // Se for admin E estiver atualizando outro usuário
    /** @var \App\Models\User|null $user */
    if (Auth::check() && Auth::user()->role === 'admin') {

    $rules['role'] = 'required|in:user,admin';
    $rules['status'] = 'sometimes|required|in:active,inactive,pending';
} else {
    $rules['role']   = 'sometimes|in:user,admin';
    $rules['status'] = 'sometimes|in:active,inactive,pending';
}

    return $rules;
}
}