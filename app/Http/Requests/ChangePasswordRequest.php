<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:8|confirmed|different:current_password',
            'new_password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'A senha atual é obrigatória.',
            'new_password.required' => 'A nova senha é obrigatória.',
            'new_password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'new_password.confirmed' => 'A confirmação da nova senha não coincide.',
            'new_password.different' => 'A nova senha deve ser diferente da senha atual.',
            'new_password_confirmation.required' => 'Confirmação obrigatória.',
            'new_password_confirmation.min' => 'A confirmação deve ter no mínimo 8 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();
        $fields = [];
        foreach ($errors as $field => $messages) {
            $fields[$field] = $messages[0]; // pega apenas a primeira mensagem por campo
        }

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Existem erros de validação',
                'fields' => $fields
            ], 422)
        );
    }
}
