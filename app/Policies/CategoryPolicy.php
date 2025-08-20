<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Ver categoria individual.
     */
    public function view(User $user, Category $category): bool
    {
        if ($user->role === 'cliente') {
            return $category->deleted_at === null && $category->status === 'active';
        }

        // admin pode ver tudo, user só vê não deletadas
        return $user->role === 'admin' || ($user->role === 'user' && $category->deleted_at === null);
    }

    /**
     * Listar categorias.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'user', 'cliente']);
    }

    /**
     * Criar categoria.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Atualizar categoria.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Soft delete.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Restaurar categoria.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Exclusão permanente.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->role === 'admin';
    }
}


