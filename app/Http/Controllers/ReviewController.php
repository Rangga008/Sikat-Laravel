<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request, OrderItem $orderItem)
{
    try {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Validasi kepemilikan order
        if (!$orderItem->order || $orderItem->order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        // Validasi status order
        if (!$orderItem->order || $orderItem->order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat memberikan review untuk pesanan yang sudah selesai'
            ], 400);
        }

        // Gunakan updateOrCreate untuk fleksibilitas
        $review = Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $orderItem->product_id,
                'order_item_id' => $orderItem->id
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null
            ]
        );

        // Update rating produk
        $this->updateProductRating($orderItem->product_id);

        return response()->json([
            'success' => true,
            'message' => $review->wasRecentlyCreated ? 'Review berhasil disimpan' : 'Review berhasil diperbarui',
            'is_update' => !$review->wasRecentlyCreated
        ]);

    } catch (\Exception $e) {
        \Log::error('Review Submission Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan review: ' . $e->getMessage()
        ], 500);
    }
}

    private function updateProductRating($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Hitung ulang rating
        $reviews = Review::where('product_id', $productId)->get();
        
        $avgRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        $product->update([
            'rating' => round($avgRating, 1),
            'review_count' => $totalReviews
        ]);
    }
}