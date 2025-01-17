<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal'
    ];

    // Relasi dengan Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi dengan Product
    public function product()
    {   
        return $this->belongsTo(Product::class);
    }
    // Relasi dengan Review
    // Relasi dengan Review
    public function review()
    {
        return $this->hasOne(Review::class)
            ->where('user_id', auth()->id());
    }

    // Method untuk mengecek apakah item bisa direview
    public function canBeReviewed()
    {
        return $this->order->status === 'completed' && 
               !$this->review()->exists();
    }
    protected $appends = ['can_be_reviewed', 'has_review'];

    public function getCanBeReviewedAttribute()
    {
        // Pastikan order sudah selesai dan belum direview
        return $this->order->status === 'completed' && 
               !$this->has_review;
    }

    public function getHasReviewAttribute()
    {
        return Review::where('user_id', auth()->id())
            ->where('product_id', $this->product_id)
            ->where('order_item_id', $this->id)
            ->exists();
    }
}