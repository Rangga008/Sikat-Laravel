<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_amount', 
        'payment_method', 
        'payment_status', 
        'status', 
        'address', 
        'phone', 
        'notes',
        'payment_proof',
        'bank_name',
        'account_name',
        'seller_confirmation_required',
        'verification_notes',
        'delivered_at'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan OrderItem
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope untuk membatasi query hanya order milik user
    public function scopeForUser(Builder $query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('user_id', $userId);
    }

    // Opsional: Global scope untuk membatasi order hanya milik user yang login
    protected static function booted()
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function (Builder $builder) {
                $builder->where('user_id', auth()->id());
            });
        }
    }
        // Di model Order
    public function getOrderNumberAttribute()
    {
        // Misalnya format: ORD-YYYY-MM-{ID}
        return 'ORD-' . 
            now()->format('Ym') . 
            '-' . 
            str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
    protected $attributes = [
        'payment_status' => 'unpaid',
        'status' => 'pending'
    ];
}