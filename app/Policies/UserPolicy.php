<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Atalho: se for admin, libera tudo.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->role === 'admin' ? true : null;
    }

    /**
     * Usuário pode ver o próprio perfil.
     */
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Só admins criam novos usuários.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Usuário pode atualizar o próprio perfil.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function updateStatus(User $authUser, User $targetUser): bool
{
    // Apenas admins podem atualizar o status de outros usuários
    return $authUser->role === 'admin';
}


    /**
     * Usuário pode deletar a si mesmo.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Restauração: só admins.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Exclusão permanente: só admins.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
