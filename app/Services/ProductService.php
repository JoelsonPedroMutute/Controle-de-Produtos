<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    /**
     * Filtra e retorna produtos de acordo com os parâmetros do request
     */
    public function findWithFilters(Request $request)
{
    $query = Product::query();

    $query->when($request->input('name'), fn($q, $name) => $q->where('name', 'like', "%$name%"))
          ->when($request->input('category_id'), fn($q, $category) => $q->where('category_id', $category))
          ->when($request->input('price_min'), fn($q, $min) => $q->where('price', '>=', $min))
          ->when($request->input('price_max'), fn($q, $max) => $q->where('price', '<=', $max))
          ->when($request->input('sku'), fn($q, $sku) => $q->where('sku', $sku))
          ->when($request->input('status'), fn($q, $status) => $q->where('status', $status));

          $sortField = $request->input('sort_field', 'id');
          $sortOrder = $request->input('sort_order', 'asc');
          $query->orderBy($sortField, $sortOrder);
          
    $perPage = $request->input('per_page', 15);
    return $query->paginate($perPage);
}


    /**
     * Cria um novo produto
     */
    public function create(array $data): Product
    {
        if (empty($data['status'])) {
            $data['status'] = 'active'; // Status padrão
        }
        return Product::create($data);
    }

    /**
     * Atualiza um produto existente
     */
    public function update(Product $product, array $data): Product
    {
         if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            throw new \InvalidArgumentException('Status inválido. Deve ser "active" ou "inactive".');
        }

        $product->update($data);
        return $product;
    }

    /**
     * Exclui um produto (soft delete)
     */
    public function delete(Product $product): void
    {
        if (! $product->exists) {
            throw new \InvalidArgumentException('Produto não encontrado.');
        }
        $product->delete();
    }

    /**
     * Restaura um produto excluído
     */
    public function restore(Product $product): Product
    {
        if (! $product->trashed()) {
            throw new \InvalidArgumentException('Produto não está excluído.');
        }
        $product->restore();
        return $product;
    }
}
