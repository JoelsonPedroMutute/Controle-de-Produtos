<?php

namespace App\Http\Controllers\Api\V1;


use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\StockMovimentResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserByIdRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
    /** @var \App\Models\User|null $user */
    // Verifica se o usuário é admin e retorna erro se for
    if ($user && $user->isAdmin()) {
        return response()->json([
            'error' => 'Admins não podem acessar esta rota.'
        ], 403);
    }

        $user = Auth::user();
        return response()->json(new UserResource($user));
    }
     
public function allUsers()
{
    $users = User::all(); // ou pode vir do seu service
    return UserResource::collection($users); // ✅ Isso filtra os campos
}



    public function changePassword(ChangePasswordRequest $request)
{
    $user = Auth::user();
    /** @var \App\Models\User|null $user */
    // Verifica se o usuário é admin e retorna erro se for
    if ($user && $user->isAdmin()) {
        return response()->json([
            'error' => 'Admins não podem acessar esta rota.'
        ], 403);
    }

    $user = Auth::user();
    $message = $this->userService->changePassword($user, $request->validated());
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
        $user = $this->userService->getUserById($id, ['stockMovements']);

        return response()->json(new UserResource($user));
    }

    public function showById(string $id): \Illuminate\Http\JsonResponse
{
    $user = $this->userService->getUserById($id);

    return response()->json([
        'message' => 'Usuário encontrado com sucesso!',
        'user' => new UserResource($user),
    ]);
}


    public function update(UpdateUserRequest $request)
{
    $user = Auth::user();

         /** @var \App\Models\User|null $user */
    if ($user->isAdmin()) {
        return response()->json([
            'error' => 'Admins não podem atualizar seus dados por esta rota.'
        ], 403);
    }

    $this->userService->updateUser($user, $request->validated());

    return response()->json([
        'message' => 'Usuário atualizado com sucesso!',
        'user' => new UserResource($user),
    ], 200);
}


  public function updateById(UpdateUserByIdRequest $request, $id)
{
      if ($request->user()?->id == $id) {
        return response()->json([
            'error' => 'Admins não podem atualizar seu próprio status ou role por este endpoint.'
        ], 403);
    }

    $this->authorizeAdmin();
    $user = $this->userService->updateUserById($id, $request->validated());

    return response()->json([
        'message' => 'Usuário atualizado com sucesso!',
        'user' => new UserResource($user),
    ], 200);
}

    public function destroySelf()
    {
         $user = Auth::user();
    /** @var \App\Models\User|null $user */

    // Verifica se o usuário é admin e retorna erro se for

    if ($user && $user->isAdmin()) {
        return response()->json([
            'error' => 'Admins não podem acessar esta rota.'
        ], 403);
    }
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
    
    public function forceDelete($id)
    {
        $this->authorizeAdmin();
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('forceDelete', $user);
        
        $user->forceDelete();
        
        return response()->json([
            'message' => 'Usuário excluído permanentemente com sucesso!'
        ], 200);
    }
    public function restore($id)

    {
        $this->authorizeAdmin();
        $user = $this->userService->restoreUser($id); // e não restoreUserById

        return response()->json([
            'message' => 'Usuário restaurado com sucesso!',
            'user' => new UserResource($user)
        ], 200);
    }

   public function changeRole(Request $request, $id)
{
    $this->authorizeAdmin(); // Só admins podem alterar cargos

    $request->validate([
        'role' => 'required|in:admin,user', // ajuste os valores conforme seu app
    ]);

    $user = $this->userService->getUserById($id);

    $user->role = $request->role;
    $user->save();

    return response()->json([
        'message' => 'Cargo atualizado com sucesso.',
        'user' => new UserResource($user),
    ]);
}



   public function updateStatus(Request $request, $id)
{
    $user = $this->userService->getUserById($id); // ou User::findOrFail($id);

    $this->authorize('updateStatus', $user); // ✅ ESTA LINHA É ESSENCIAL

    $user = $this->userService->updateUserStatus($id, $request->validate([
        'status' => 'required|in:active,inactive,pending',
    ]));

    return response()->json([
        'message' => 'Status do usuário atualizado com sucesso!',
        'user' => new UserResource($user)
    ], 200);
}

   public function authorizeAdmin(): void
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!$user || !$user->isAdmin()) {
        abort(403, 'Acesso negado. Você não tem permissão para acessar este recurso.');
    }
}

   public function stockMoviments($id)
   {
       $this->authorizeAdmin();
       $user = $this->userService->getUserById($id, ['stockMovements']);
       
       return response()->json([
           'user' => new UserResource($user),
           'stock_movements' => StockMovimentResource::collection($user->stockMovements)
       ]);
   }

   public function changeRole(Request $request, $id)
{
    $this->authorizeAdmin();
    $user = $this->userService->getUserById($id);
    
    $data = $request->validate([
        'role' => 'required|in:admin,user',
    ]);
    
    $user->role = $data['role'];
    $user->save();
    
    return response()->json([
        'message' => 'Função do usuário atualizada com sucesso!',
        'user' => new UserResource($user)
    ], 200);
}
}
