<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash

class UserController extends Controller
{

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware(['auth:sanctum', 'active.user']);
        $this->userService = $userService;
    }

    public function index(Request $request, UserFilter $filter)
    {
        $this->authorizeAdmin();
        $users = $this->userService->getAllFiltered($filter, $request);
        return response()->json(UserResource::collection($users));
    }
    public function profile()
    {
        $user = Auth::user();
        return response()->json(new UserResource($user));
    }

    public function changePassword(changePasswordReques $request);
    {
        $message = $this->UserService->changePassword($request);
        return response()->json(['message' => $message]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json([
            'message' => 'Usuário criado com sucesso!',
            'user' => new UserResource($user)
        ], 201 );
    }

    public function show($id)
    {
        $this->authorizeAdmin();
        $user = $this->userService->getUserById($id);
        return response()->json(new UserResource($user));
    }

    public function update(UpdsateUserRequest $request)
    {
        $user = Auth::user();
        $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'user' => new UserResource($user),
        ], 200);
    }

    public function updateById(UpdateUserByIdRequest $request, $id)
    {
       if (auth()->id() == $id) {
           return response()->json([
               'error' => ' Admins não podem atualizar seus proprio status, ou role por este endpoint.'
           ], 403);
       }

       $this->authorizeAdmin();
       User = $this->userSerive->updateUserById($id, $request->validated());
       return response()->json([
           'message' => 'Usuário atualizado com sucesso!',
           'user' => new UserResource($user),
       ], 200);
    }

    public function destroySelf()
    {
        $user = Auth::user();
        $this->userService->deleteUser($user);
        return response()->json([
            'message' => 'Usuário deletado com sucesso!'
        ], 200);
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $message = $this->userService->deleteUserById($id);
        return response()->json([
            'message' => $message
        ], 200);

 }
    public funtion restore($id)
    {
        $this->authorizeAdmin();
        $user = $this->userService->restoreUserById($id);
        return response()->json([
            'message' => 'Usuário restaurado com sucesso!',
            'user' => new UserResource($user)
        ], 200);
    }
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();
        $user = $this->userService->updateUserStatus($id, $request->validated([
            'status' => 'required|in:active,inactive,pending',
        ]));
        return response()->json([
            'message' => 'Status do usuário atualizado com sucesso!',
            'user' => new UserResource($user)
        ], 200);
    }

     public function authorizeAdmin(): void
     {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar este recurso.');
        }
     }
}


