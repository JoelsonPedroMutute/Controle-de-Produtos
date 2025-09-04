<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use LaravelLang\Publisher\Console\Update;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->middleware(['auth:sanctum', 'active.user']);
        $this->productService = $productService;
    }

    // Lista produtos com filtros
    public function index(Request $request)
    {
        $filter = new ProductFilter($request);
        $products = $this->productService->getAllFiltered($filter, $request);

        return response()->json([
            'message' => 'Produtos encontrados',
            'data' => ProductResource::collection($products)
        ], 200);
    }

    //  Mostra um produto específico
    public function show($id, Request $request)
    {
        $filter = new ProductFilter($request);
        $product = $this->productService->getById($id, $filter);

        return response()->json([
            'message' => 'Produto encontrado',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Cria um novo produto
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data' => new ProductResource($product)
        ], 201);
    }

    //  Atualiza um produto existente
    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->validated());

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Atualiza a descrição de um produto existente
    public function updateDescription(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->validated());

        return response()->json([
            'message' => 'Descrição do produto atualizada com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Atualiza o status de um produto existente
    public function updateStatus(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateStatus($id, $request->validated());

        return response()->json([
            'message' => 'Status do produto atualizado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Atualiza o nome de um produto existente
    public function updateName(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateName($id, $request->validated());

        return response()->json([
            'message' => 'Nome do produto atualizado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Atualiza as categorias de um produto existente
    public function updateCategories(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateCategories($id, $request->validated());

        return response()->json([
            'message' => 'Categorias do produto atualizadas com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Exclui um produto (soft delete)
    public function destroy($id)
    {
        $message = $this->productService->delete($id);

        return response()->json([
            'message' => $message
        ], 200);
    }

    //  Restaura um produto excluído
    public function restore($id)
    {
        $product = $this->productService->restore($id);

        return response()->json([
            'message' => 'Produto restaurado com sucesso',
            'data' => new ProductResource($product)
        ], 200);
    }

    //  Exclusão permanente
    public function forceDelete($id)
    {
        $message = $this->productService->forceDelete($id);

        return response()->json([
            'message' => $message
        ], 200);
    }
     
}
