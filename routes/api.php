<?php

use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\ActiveUser;
use App\Http\Middleware\CheckUserStatus;

// Middleware para verificar se o usuário é admin
// Rotas públicas da API v1
Route::prefix('v1')->group(function () {

    // Autenticação pública
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // Rotas protegidas (usuário autenticado e ativo)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

        // Logout
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Rotas para usuário comum autenticado
        Route::prefix('user')->middleware('is.user')->group(function () {
            Route::get('/', [UserController::class, 'profile']);
            Route::put('/', [UserController::class, 'update']);
            Route::delete('/', [UserController::class, 'destroySelf']);
            Route::patch('/change-password', [UserController::class, 'changePassword']);
            //  Route::get('user/stock-moviments', [UserController::class, 'stockMovimentsSelf']);
        });

        // Rotas administrativas (prefixo completo: /api/v1/admin/users)
        Route::middleware('is.admin')->prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
    
        // Atualizar perfil do admin autenticado
        Route::put('user', [UserController::class, 'updateAdminProfile']);
        Route::get('/admin', [UserController::class, 'indexAdmin']);
        Route::get('/profile', [UserController::class, 'adminProfile']);


    
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/admin/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'updateById']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::delete('/{id}/force', [UserController::class, 'forceDelete']);

    Route::patch('/{id}/restore', [UserController::class, 'restore']); 
        Route::patch('/{id}/role', [UserController::class, 'changeRole']);
        Route::patch('/{id}/status', [UserController::class, 'updateStatus']);
        Route::patch('/{id}/password', [UserController::class, 'changePassword']);
        Route::patch('/change-password', [UserController::class, 'changeAdminPassword']);
        Route::get('/{id}/stock-moviments', [UserController::class, 'stockMoviments']);

});

    });
});
