<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
    protected $fillable = [
        'user_id', 
        'product_id', 
        'order_item_id', 
        'rating', 
        'comment'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi dengan OrderItem (opsional)
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Method untuk mendapatkan rating terbaru per user
    public static function getLatestProductRating($productId)
    {
        // Ambil review terbaru per user
        $latestReviews = self::where('product_id', $productId)
            ->select(DB::raw('MAX(id) as max_id'), 'user_id')
            ->groupBy('user_id')
            ->get()
            ->pluck('max_id');

        // Hitung rata-rata rating dari review terbaru
        $avgRating = self::whereIn('id', $latestReviews)
            ->avg('rating');

        return round($avgRating, 1) ?: 0;
    }

    // Metode untuk membersihkan review lama
    public static function cleanupOldReviews($productId, $userId)
    {
        // Ambil review terbaru
        $latestReview = self::where('product_id', $productId)
            ->where('user_id', $userId)
            ->latest()
            ->first();

        // Hapus review lama
        if ($latestReview) {
            self::where('product_id', $productId)
                ->where('user_id', $userId)
                ->where('id', '!=', $latestReview->id)
                ->delete();
        }
    }
    // Mutator untuk validasi rating
    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = max(1, min(5, $value));
    }

    // Scope untuk query
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}