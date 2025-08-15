<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Filters\CategoryFilter;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        // Autoriza via Policy
        $this->authorize('viewAny', Category::class);

        // Verifica se o usuário é admin (se método existir)
        if (method_exists($this, 'authorizeAdmin')) {
            $this->authorizeAdmin();
        }

        $filter = new CategoryFilter($request);
        $categories = $this->categoryService->getAllFiltered($filter, $request);

        return response()->json([
            'message' => 'Categorias encontradas',
            'data' => CategoryResource::collection($categories)
        ], 200);
    }
}
