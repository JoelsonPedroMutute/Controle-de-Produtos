<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
  public function rules(): array
{
    return [
        'name'    => 'required|string|max:255',
        'email'   => [
            'required',
            'email',
            Rule::unique('users', 'email'), // ← corrigido aqui
        ],
        'password' => 'required|string|min:6',
        'role'    => 'required|in:user,admin',
        'address' => 'nullable|string|max:255',
        'phone'   => 'nullable|string|max:20',
    ];
}
    protected function failedValidation(Validator $validator)
{
    $errors = $validator->errors();

    throw new HttpResponseException(response()->json([
        'status' => 'error',
        'message' => $errors->first(), // primeira mensagem de erro
    ], 422));
}

}