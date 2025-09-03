<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Filters\CategoryFilter;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Listar categorias com filtros aplicados
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $categories = $this->categoryService->findWithFilters($request);

        return response()->json([
            'message' => 'Categorias encontradas',
            'data' => CategoryResource::collection($categories)
        ], 200);
    }

    /**
     * Criar nova categoria
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Categoria criada com sucesso',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Atualizar categoria existente
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $this->authorize('update', $category);

        // Evitar duplicação de nomes
        if (
            $request->filled('name') &&
            Category::where('name', $request->name)
                ->where('id', '!=', $id)
                ->exists()
        ) {
            return response()->json([
                'message' => 'Já existe outra categoria com esse nome'
            ], 409);
        }

        $updatedCategory = $this->categoryService->update($id, $request->validated());

        return response()->json([
            'message' => 'Categoria atualizada com sucesso',
            'data' => new CategoryResource($updatedCategory)
        ], 200);
    }

    /**
     * Mostrar detalhes de uma categoria
     */
    public function show($id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $this->authorize('view', $category);

        // Cliente só pode ver categorias ativas e não deletadas
        if (Auth::user()->role === 'cliente' && ($category->status !== 'active' || $category->trashed())) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Categoria encontrada',
            'data' => new CategoryResource($category)
        ], 200);
    }

    public function products($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $this->authorize('view', $category);

        return response()->json([
            'message' => 'Produtos da categoria encontrados',
            'data' => $category->products
        ], 200);
    }

 
    /**
     * Deletar categoria (soft delete)
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $this->authorize('delete', $category);

        $this->categoryService->delete($id);

        return response()->json([
            'message' => 'Categoria deletada com sucesso'
        ], 200);
    }

    /**
     * Restaurar categoria deletada
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $this->authorize('restore', $category);

        if (!$category->trashed()) {
            return response()->json([
                'message' => 'Categoria não está deletada'
            ], 400);
        }

        $restoredCategory = $this->categoryService->restore($id);

        return response()->json([
            'message' => 'Categoria restaurada com sucesso',
            'data' => new CategoryResource($restoredCategory)
        ], 200);
    }
}
