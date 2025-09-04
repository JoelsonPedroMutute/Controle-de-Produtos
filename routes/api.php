<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rotas públicas da API v1
Route::prefix('v1')->group(function () {

    // 🔑 Autenticação pública
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // 🔒 Rotas protegidas (usuário autenticado e ativo)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

        // Logout
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // 👤 Rotas de usuário comum autenticado
        Route::prefix('user')->middleware('is.user')->group(function () {
            Route::get('/', [UserController::class, 'profile']);          // perfil (user ou admin)
            Route::put('/', [UserController::class, 'update']);           // atualizar perfil
            Route::delete('/', [UserController::class, 'destroySelf']);   // deletar conta (soft delete)
            Route::patch('/change-password', [UserController::class, 'changePassword']); // alterar senha
            Route::get('/stock-moviments', [UserController::class, 'stockMovimentsSelf']); // estoque do próprio usuário
        });

        // 👨‍💼 Rotas administrativas de usuários
        Route::middleware('is.admin')->prefix('admin/users')->group(function () {
            Route::get('/', [UserController::class, 'index']);           // listar com filtros
            Route::get('/all', [UserController::class, 'allUsers']);     // listar todos (sem filtros)
            Route::post('/', [UserController::class, 'store']);          // criar usuário
            Route::get('/{id}', [UserController::class, 'show']);        // detalhar usuário
            Route::put('/{id}', [UserController::class, 'updateById']);  // atualizar usuário
            Route::delete('/{id}', [UserController::class, 'destroy']);  // soft delete
            Route::delete('/{id}/force', [UserController::class, 'forceDelete']); // force delete (apenas admin)
            Route::patch('/{id}/restore', [UserController::class, 'restore']);
            Route::patch('/{id}/role', [UserController::class, 'changeRole']);
            Route::patch('/{id}/status', [UserController::class, 'updateStatus']);
            Route::patch('/password', [UserController::class, 'changePassword']);
            Route::get('/{id}/stock-moviments', [UserController::class, 'stockMoviments']);

            // Perfil do próprio admin
            Route::get('/profile', [UserController::class, 'profile']);
            Route::put('/profile', [UserController::class, 'updateAdminProfile']);
             Route::patch('/profile/change-password', [UserController::class, 'changePassword']);

        });
    });

    // 📂 Rotas para categorias
    Route::prefix('categories')->middleware(['auth:sanctum', 'active.user'])->group(function () {
        // Usuário comum -> apenas leitura
        Route::middleware('is.user')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::get('/{id}/products', [CategoryController::class, 'products']);
        });

        // Admin -> CRUD
        Route::middleware('is.admin')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::patch('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
            Route::patch('/{id}/restore', [CategoryController::class, 'restore']);
            
        });
    });

    // 📦 Rotas para produtos
    Route::prefix('products')->middleware(['auth:sanctum', 'active.user'])->group(function () {
        // Usuário comum -> apenas leitura
        Route::middleware('is.user')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::get('/{id}/categories', [ProductController::class, 'categories']);
            Route::get('/{id}/stock-moviments', [ProductController::class, 'stockMoviments']);
            Route::patch('/{id}/status', [ProductController::class, 'updateStatus']);
            Route::patch('/{id}/description', [ProductController::class, 'updateDescription']);
            Route::patch('/{id}/name', [ProductController::class, 'updateName']);
          

        });


        // Admin -> CRUD
        Route::middleware('is.admin')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::patch('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
            Route::patch('/{id}/restore', [ProductController::class, 'restore']);
            Route::patch('/{id}/status', [ProductController::class, 'updateStatus']);
            Route::patch('/{id}/categories', [ProductController::class, 'updateCategories']);
            Route::patch('/{id}/price', [ProductController::class, 'updatePrice']);
            Route::patch('/{id}/sku', [ProductController::class, 'updateSku']);
            Route::patch('/{id}/description', [ProductController::class, 'updateDescription']);
            Route::patch('/{id}/name', [ProductController::class, 'updateName']);
            Route::delete('/{id}/force', [ProductController::class, 'forceDelete']);
        });
    });

});
