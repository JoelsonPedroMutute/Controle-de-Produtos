<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
  public function createUser(array $data): User
  {
    $existingUser = User::withTrashed()->where('email', $data['email'])->first();

    if($existingUser)
    {
        throw ValidationException::withMessages([
            'email' => 'Um usuário com este e-mail já existe.',
        ], 422);
    }

    $data['password'] = Hash::make($data['password']);
    $user = User::create($data);
    return $user;
  }

  public function updateUser(User $user, array $data):void
  {
    $user->update($data);
  }

    public function updateUserById(int $id, array $data): ?User
    {
        $authUser = Auth()->user();
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
            ], 422);
        }
        $user->delete();
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

    public function restoreUser(int $id): user
    {
        $user = User::withTrashed()->findOrFail($id);
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

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function getAllFiltered(UserFilter $filter, Request $request): string
    {
        $query = User::query();
        return $filter->apply($query, $request->all())->paginate(10);
    }
    
    public function changePassword(User $user, array $data): User
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'A senha atual está incorreta.',
            ], 422);
        }
        $user->password = bcrypt($request->input('new_password'));
        $user->save();

    return 'Senha alterada com sucesso.';
    }
        
    
    

}
