<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        // Autorização para ver qualquer produto
        $this->authorize('viewAny', Product::class);

        $this->productService = $productService;
    }

    // Lista produtos com filtros
    public function index(Request $request)
    {
        $products = $this->productService->findWithFilters($request);

        return response()->json([
            'message' => 'Produtos encontrados',
            'data' => ProductResource::collection($products)
        ], 200);
    }

    // Mostra um produto específico
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return response()->json([
            'message' => 'Produto encontrado',
            'data' => new ProductResource($product)
        ], 200);
    }

    // Cria um novo produto
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->create($request->validated());

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data' => new ProductResource($product)
        ], 201);
    }

    // Atualiza um produto existente
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $product = $this->productService->update($product, $request->validated());

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    // Exclui um produto (soft delete)
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return response()->json([
            'message' => 'Produto excluído com sucesso'
        ], 204);
    }

    // Restaura um produto excluído
    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $this->authorize('restore', $product);
        $this->productService->restore($product);

        return response()->json([
            'message' => 'Produto restaurado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }
}
