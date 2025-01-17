<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user)
    {
        return true; // Semua user bisa melihat daftar produk
    }

    public function view(?User $user, Product $product)
    {
        // Jika produk tersedia atau user adalah admin
        return $product->is_available || 
               ($user && $user->hasRole('admin'));
    }

    public function create(User $user)
    {
        return $user->hasRole('admin') || 
               $user->hasRole('seller');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('seller') && $product->user_id === $user->id);
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('seller') && $product->user_id === $user->id);
    }
}