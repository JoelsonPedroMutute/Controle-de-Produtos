<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateProductRequest extends FormRequest
{
    /**
     * Permite que qualquer usuário autorizado envie a requisição
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Executa antes das regras de validação para checar UUID
     */
    protected function prepareForValidation(): void
    {
        $productId = $this->route('id');

        if (!Str::isUuid($productId)) {
            throw ValidationException::withMessages([
                'id' => ['O identificador informado é inválido.'],
            ]);
        }
    }

    /**
     * Regras de validação para atualização de produto
     */
    public function rules(): array
    {
        $productId = $this->route('id'); // UUID já validado

        $isPatch = $this->isMethod('patch');

        return [
            'name' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [$isPatch ? 'sometimes' : 'nullable', 'string'],
            'price' => [$isPatch ? 'sometimes' : 'required', 'numeric', 'min:0.01'],
            'status' => [$isPatch ? 'sometimes' : 'required', 'in:active,inactive'],
            'category_id' => [$isPatch ? 'sometimes' : 'required', 'exists:categories,id'],
            'quantity' => [$isPatch ? 'sometimes' : 'required', 'integer', 'min:0'],
            'sku' => [$isPatch ? 'sometimes' : 'nullable', 'string', 'max:50'],
        ];
    }
}
