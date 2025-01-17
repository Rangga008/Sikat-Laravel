<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */

    public function update(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }
    public function viewSalesReport(User $user)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }

    public function manageSales(User $user)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }

    public function view(User $user, Order $order)
    {
        // Hanya pemilik order atau admin/restaurant yang bisa melihat
        return $user->id === $order->user_id || 
               $user->hasRole(['admin', 'restaurant']);
    }

    public function updateOrderStatus(User $user, Order $order)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }
}