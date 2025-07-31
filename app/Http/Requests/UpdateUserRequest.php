<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $userId = $this->route('user'); // ou 'id' dependendo de como está nomeado na rota

        return [
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role'    => 'required|in:user,admin',
            'status'  => 'required|in:active,inactive,pending',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
        ];
    }
}
