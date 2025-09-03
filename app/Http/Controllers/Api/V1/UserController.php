<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserByIdRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\StockMovimentResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware(['auth:sanctum', 'active.user']);
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        
        $filter = new UserFilter($request);
        $users = $this->userService->getAllFiltered($filter, $request);

        return response()->json([
            'message' => 'Usuários encontrados',
            'data' => UserResource::collection($users)
        ]);
    }

    public function allUsers(Request $request)
{
   
    $filter = new UserFilter($request);
    $users = $this->userService->getAllFiltered($filter, $request);
    return UserResource::collection($users);
}

    public function profile()
    {
        $user = Auth::user();
        return response()->json(new UserResource($user), 200);
    }

    public function adminProfile()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser->isAdmin()) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }
        return response()->json(new UserResource($authUser), 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $authUser = Auth::user();
        $message = $this->userService->changePassword($authUser, $request->validated());
        return response()->json(['message' => $message], 200);
    }

    public function changeAdminPassword(ChangePasswordRequest $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser->isAdmin()) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }
        $message = $this->userService->changePassword($authUser, $request->validated());
        return response()->json(['message' => $message], 200);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json([
            'message' => 'Usuário criado com sucesso!',
            'user' => new UserResource($user)
        ], 201);
    }

    public function show($id, Request $request)
    {
        
        $filter = new UserFilter($request);
        $user = $this->userService->getUserById($id, $filter);

        return response()->json(new UserResource($user));
    }

    public function showById($id, Request $request)
    {
        $filter = new UserFilter($request);
        $user = $this->userService->getUserById($id, $filter);

        return response()->json([
            'message' => 'Usuário encontrado com sucesso!',
            'user' => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if ($authUser->isAdmin()) {
            return response()->json([
                'error' => 'Admins não podem atualizar seus dados por esta rota.'
            ], 403);
        }

        $this->userService->updateUser($authUser, $request->validated());

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'user' => new UserResource($authUser),
        ], 200);
    }

    public function updateById(UpdateUserByIdRequest $request, $id)
    {
       

        if ($request->user()?->id == $id) {
            return response()->json([
                'error' => 'Admins não podem atualizar seu próprio status ou role por este endpoint.'
            ], 403);
        }

        $user = $this->userService->updateUserById($id, $request->validated());


        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'user' => new UserResource($user),
        ], 200);
    }

    public function destroySelf()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if ($authUser->isAdmin()) {
            return response()->json([
                'error' => 'Admins não podem acessar esta rota.'
            ], 403);
        }

        $this->userService->deleteUser($authUser);
        return response()->json(['message' => 'Usuário deletado com sucesso!'], 200);
    }

    public function destroy($id)
    {
        
        $message = $this->userService->deleteUserById($id);

        return response()->json(['message' => $message], 200);
    }

    public function forceDelete($id)
    {
        
        $message = $this->userService->forceDeleteById($id);

        return response()->json(['message' => $message], 200);
    }

    public function restore($id)
    {
        
        $user = $this->userService->restoreUser($id);

        return response()->json([
            'message' => 'Usuário restaurado com sucesso!',
            'user' => new UserResource($user)
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        
        $filter = new UserFilter($request);
        $user = $this->userService->getUserById($id, $filter);

        $user = $this->userService->updateUserStatus($id, $request->validate([
            'status' => 'required|in:active,inactive,pending',
        ]));

        return response()->json([
            'message' => 'Status do usuário atualizado com sucesso!',
            'user' => new UserResource($user)
        ], 200);
    }
    public function updateAdminProfile(UpdateUserRequest $request)
{
    $user = $request->user();

    // Chama o serviço, não o método do controller
    $updatedUser = $this->userService->updateAdminProfile($user, $request->validated());

    return response()->json([
        'message' => 'Perfil do admin atualizado com sucesso!',
        'user' => new UserResource($updatedUser)
    ], 200);
}


    public function stockMoviments($id, Request $request)
    {
        
        $filter = new UserFilter($request);
        $user = $this->userService->getUserById($id, $filter);

        return response()->json([
            'user' => new UserResource($user),
            'stock_movements' => StockMovimentResource::collection($user->stockMovements)
        ]);
    }

    public function changeRole(Request $request, $id)
{
    // Valida que o role exista e seja 'user' ou 'admin'
    $request->validate([
        'role' => 'required|in:user,admin',
    ]);

    // Chama o service para alterar o role do usuário
    $user = $this->userService->updateRole($id, $request->role);

    // Retorna a resposta JSON usando o resource
    return response()->json([
        'message' => 'Role alterado com sucesso',
        'user' => new UserResource($user)
    ]);
}

    public function stockMovimentsSelf()
    {
    $user = Auth::user();
    return response()->json([
        'user' => new UserResource($user),
        'stock_movements' => StockMovimentResource::collection($user->stockMovements)
    ]);
}

}