<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policies vão cuidar disso
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('category_id', $this->category_id);
                }),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'category_id' => ['required', 'exists:categories,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
        ];
    }
}
