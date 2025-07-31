<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            Rule::unique('users', 'email')->ignore($this->route('user')),
        ],
        'role'    => 'required|in:user,admin',
        'status'  => 'required|in:active,inactive,pending',
        'address' => 'nullable|string|max:255',
        'phone'   => 'nullable|string|max:20',
    ];
}
}
