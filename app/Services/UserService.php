<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
 public function createUser(array $data): User
{
    $existingUser = User::withTrashed()->where('email', $data['email'])->first();

    if ($existingUser) {
        throw ValidationException::withMessages([
            'email' => 'Um usuário com este e-mail já existe.',
        ]);
    }

    // Corrigindo a senha corretamente
    $data['password'] = Hash::make($data['password']);

    // Agora cria o usuário com todos os dados, incluindo status vindo da request
    return User::create($data);
}


    public function updateUserById(int $id, array $data): ?User
    {
        $authUser = Auth::user();
        if($authUser->id == $id)
        {
            throw ValidationException::withMessages([
                'user' => 'Admins não podem modificar a si mesmo por este endpoint.',
            ], 422);
        }
        
         $user = User::findOrFail($id);
            $user->update($data);
    
        return $user;
    }

    public function deleteUser(User $user): void
    {
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'user' => 'Usuário inativo não pode deletar a conta.',
            ]);
        }
        $user->delete();
    }

    public function updateUser(User $user, array $data): void
        {
    // Garante que role e status não sejam atualizados aqui
    unset($data['role'], $data['status']);

    $user->update($data);
        }

    
    public function deleteUserById(int $id): string
    {

        $user = User::findOrFail($id);

        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'user' => 'Usuário já foi deletado.',
            ], 422);
        }

        $user->delete();

        return 'Usuário deletado com sucesso.';
    }

    public function restoreUser(int $id): User
{
    $user = User::withTrashed()->findOrFail($id);

    if (!$user->trashed()) {
        throw ValidationException::withMessages([
            'user' => 'Usuário não está deletado.',
        ]);
    }

    $user->restore();
    return $user;
}


    public function updateUserStatus(int $id, array $status): User
    {
        $user = User::findOrFail($id);
        $user->status = $status['status'];
        $user->save();
        return $user;
    }

   public function getUserById(int $id, array $with = []): User
{
    return User::with($with)->findOrFail($id);
}


    public function getAllFiltered(UserFilter $filter, Request $request): LengthAwarePaginator

    {
        $query = User::query();
        return $filter->apply($query, $request->all())->paginate(10);
    }
    
    public function changePassword(User $user, array $data): string
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'A senha atual está incorreta.',
            ], 422);
        }
        $user->password = Hash::make($data['new_password']);
        $user->save();

    return 'Senha alterada com sucesso.';
    }
        
    
    

}
