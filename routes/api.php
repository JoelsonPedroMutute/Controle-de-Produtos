<?php

use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ROTAS DE AUTENTICAÇÃO
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
   // Usuário comum
   Route::get('/users', [UserController::class, 'index']);
   Route::post('/users', [UserController::class, 'store']);
   Route::get('/users/{id}', [UserController::class, 'show']);
   Route::put('/users/{id}', [UserController::class, 'update']);
   Route::delete('/users/{id}', [UserController::class, 'destroySelf']);
});
