<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Admin\UserManagementController;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $guard_name = 'web';
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo 
            ? asset('storage/' . $this->profile_photo)
            : asset('default-avatar.png');
    }
    public function isRestaurant()
    {
        return $this->role === 'restaurant';
    }
    // Tambahkan relasi produk
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    // Tambahkan method fallback jika tidak ada produk
    public function getProductsAttribute()
    {
        return $this->products()->get() ?? collect([]);
    }
}