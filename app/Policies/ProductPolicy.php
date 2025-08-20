<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        // Clientes podem ver todos os produtos ativos
       if($user->role === 'cliente')
       {
        return true; 
       }

        // Admins podem ver todos os produtos
        return $user->hasPermission('view_any_product');
        
    }

    public function view(User $user, Product $product)
    {
        // Clientes podem ver apenas produtos ativos
        if($user->role === 'cliente')
        {
            return $product->status === 'active';
        }
        // Admins podem ver todos os produtos
        return $user->hasPermission('view_product') || $user->id === $product->user_id;
    }

    public function create(User $user)
    {
        return $user->hasPermission('create_product');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasPermission('update_product') || $user->id === $product->user_id;
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasPermission('delete_product') || $user->id === $product->user_id;
    }
}
