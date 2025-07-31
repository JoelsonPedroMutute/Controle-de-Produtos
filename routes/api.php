<?php
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Rotas protegidas (usuário autenticado e ativo)
Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

    // Logout
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Rotas para usuário comum autenticado
    Route::prefix('v1')->group(function () {
        Route::get('user', [UserController::class, 'profile']); // Info do próprio usuário
        Route::put('user', [UserController::class, 'update']); // Atualiza o próprio usuário
        Route::delete('user', [UserController::class, 'destroySelf']); // Exclui o próprio usuário
        Route::post('user/change-password', [UserController::class, 'changePasswordSelf']); // Troca senha
        Route::get('user/stock-moviments', [UserController::class, 'stockMovimentsSelf']); // Mov. de estoque do próprio usuário
    });

    // Rotas administrativas para gerenciar usuários
    Route::middleware('can:is-admin')->prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'allUsers']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'showById']);
        Route::put('/{id}', [UserController::class, 'updateById']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::delete('/{id}/force', [UserController::class, 'forceDelete']);
        Route::post('/{id}/restore', [UserController::class, 'restore']);
        Route::post('/{id}/change-role', [UserController::class, 'changeRole']);
        Route::post('/{id}/change-status', [UserController::class, 'changeStatus']);
        Route::post('/{id}/change-password', [UserController::class, 'changePassword']);
        Route::get('/{id}/stock-moviments', [UserController::class, 'stockMoviments']);
    });
}); 

