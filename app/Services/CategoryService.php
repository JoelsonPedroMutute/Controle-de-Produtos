<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Filters\CategoryFilter;
use Illuminate\Http\Request;

class CategoryService
{
    /**
     * Criar nova categoria
     */
    public function create(array $data): Category
    {
        // Se não definir status, assume 'active'
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return Category::create($data);
    }

    /**
     * Atualizar categoria existente
     */
    public function update(int $id, array $data): ?Category
    {
        $category = Category::find($id);

        if (!$category) {
            return null;
        }

        $category->update($data);
        return $category;
    }

    /**
     * Deletar categoria (soft delete)
     */
    public function delete(int $id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    /**
     * Restaurar categoria deletada
     */
    public function restore(int $id): ?Category
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return null;
        }

        $category->restore();
        return $category;
    }

    /**
     * Buscar categorias aplicando filtros e regras por role
     */
    public function findWithFilters(Request $request)
{
    $query = Category::query();
    $role = Auth::user()->role;

    if ($role === 'cliente') {
        // Cliente → só categorias ativas e não deletadas
        $query->where('status', 'active')->whereNull('deleted_at');
    }

    // Aplica os filtros corretamente
    $filter = new CategoryFilter($request);
    return $filter->apply($query)->get();
}

}
