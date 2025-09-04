<?php

namespace App\Services;

use App\Models\Product;
use App\Filters\ProductFilter;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ProductService
{
    /**
     * Lista todos os produtos aplicando filtros e paginação
     */
    public function getAllFiltered(ProductFilter $filter, Request $request)
    {
        $query = Product::query();

        // Aplica filtros customizados
        $query = $filter->apply($query);

        // Ordenação
        $sortField = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        // Paginação
        $perPage = $request->input('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Retorna um produto por ID, aplicando filtros se necessário
     */
    public function getById(string $id, ProductFilter $filter): Product
    {
        $query = Product::query()->where('id', $id);

        $query = $filter->apply($query);

        return $query->firstOrFail();
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
    public function update(string $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        // 🚫 Bloqueia atualização se inativo
        if ($product->status === 'inactive') {
            throw new \Exception('Este produto está inativo e não pode ser alterado.');
        }

        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            throw new \InvalidArgumentException('Status inválido. Deve ser "active" ou "inactive".');
        }

        $product->update($data);
        return $product;
    }

    /**
     * Atualiza somente a descrição
     */
    public function updateDescription(string $id, array $data): Product
    {
        if (empty($data['description'])) {
            abort(422, 'A descrição não pode estar vazia.');
        }

        $product = Product::findOrFail($id);

        if ($product->status === 'inactive') {
            abort(403, 'Este produto está inativo e não pode ter a descrição alterada.');
        }

        $product->update(['description' => $data['description']]);
        return $product;
    }


    /**
     * Atualiza somente o status
     */
    public function updateStatus(string $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        if (empty($data['status'])) {
            throw new \InvalidArgumentException('O status não pode estar vazio.');
        }

        if (!in_array($data['status'], ['active', 'inactive'])) {
            throw new \InvalidArgumentException('Status inválido. Deve ser "active" ou "inactive".');
        }

        // Permite atualizar o status mesmo se estiver inativo
        $product->update(['status' => $data['status']]);
        return $product;
    }

    public function updateName(string $id, array $data): Product
    {
        if (empty($data['name'])) {
            abort(422, 'O nome não pode estar vazio.');
        }

        $product = Product::findOrFail($id);

        if ($product->status === 'inactive') {
            abort(403, 'Este produto está inativo e não pode ter o nome alterado.');
            
        }
        return $product;
    }

    public function updateCategories(string $id, array $data): Product
{
    $product = Product::findOrFail($id);

    if ($product->status === 'inactive') {
        abort(403, 'Este produto está inativo e não pode ter as categorias alteradas.');
    }

    // Se for apenas uma categoria
    if (empty($data['category_id'])) {
        abort(422, 'A categoria não pode estar vazia.');
    }

    // Atualiza a categoria do produto
    $product->update(['category_id' => $data['category_id']]);

    return $product->load('category'); // retorna produto com a categoria carregada
}


    /**
     * Exclui (soft delete) um produto
     */
    public function delete(int $id): string
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return 'Produto excluído com sucesso';
    }

    /**
     * Restaura um produto excluído
     */
    public function restore(int $id): Product
    {
        $product = Product::withTrashed()->findOrFail($id);

        if (! $product->trashed()) {
            throw new \InvalidArgumentException('Produto não está excluído.');
        }

        $product->restore();
        return $product;
    }

    /**
     * Exclusão permanente de um produto
     */
    public function forceDelete(int $id): string
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();

        return 'Produto removido permanentemente';
    }
}
