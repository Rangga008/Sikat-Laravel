<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // Tambahkan import ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'stock',
        'rating',
        'sold_count',
        'image',
        'is_available',
        'category_id'
    ];
    protected $casts = [
        'rating' => 'float',
        'is_available' => 'boolean'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi dengan review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Accessor untuk rating
    public function getRatingAttribute($value)
    {
        return round($value, 1);
    }
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : asset('default-product.png');
    }
    // Accessor untuk path absolut
    public function getAbsoluteImagePathAttribute()
    {
        if ($this->image) {
            return storage_path('app/public/' . $this->image);
        }
        
        return null;
    }
    // Tambahkan append untuk accessor
    protected $appends = ['image_url', 'absolute_image_path'];
}