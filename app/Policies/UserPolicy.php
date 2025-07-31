<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Se for admin, libera tudo — exceto em casos tratados abaixo.
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
     * Apenas admins criam novos usuários.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Usuário pode atualizar seu perfil, e admin pode atualizar qualquer um, exceto ele mesmo.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true; // atualizando a si mesmo
        }

        return $user->role === 'admin' && $user->id !== $model->id;
    }

    /**
     * Só admins podem alterar status de outros, mas nunca o próprio.
     */
    public function updateStatus(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }

    /**
     * Usuário pode deletar a si mesmo.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Restauração de usuários: só admins.
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

    /**
     * Trocar senha: o próprio usuário.
     */
    public function changePassword(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
