<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProductController extends Controller
{
    public function index()
{
    $products = Product::where('user_id', auth()->id())->paginate(10);
    return view('admin.products.index', compact('products'));
}

public function create()
{
    // Ambil semua kategori untuk dropdown
    $categories = Category::all();
    return view('admin.products.create', compact('categories'));
}

public function edit(Product $product)
    {
        // Ambil semua kategori untuk dropdown
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['user_id'] = auth()->id();
        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
    }
    


    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus');
    }
    use AuthorizesRequests;

    public function show(Product $product)
    {
        // Pastikan otorisasi
        $this->authorize('view', $product);

        // Ambil review terbaru per user
        $reviews = Review::where('product_id', $product->id)
            ->with('user')
            ->select(DB::raw('MAX(id) as max_id'), 'user_id')
            ->groupBy('user_id')
            ->orderByDesc('max_id')
            ->get()
            ->map(function($item) {
                return Review::find($item->max_id);
            });

        // Update rating produk dengan rating terbaru
        $product->rating = Review::getLatestProductRating($product->id);
        $product->save();

        // Ambil produk terkait
        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->take(4)
            ->get();

        // Log untuk debugging
        Log::info('Product ID: ' . $product->id);
        Log::info('Updated Product Rating: ' . $product->rating);
        Log::info('Reviews in View (Count): ' . $reviews->count());

        return view('products.show', [
            'product' => $product,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts
        ]);
    }
public function addReview(Request $request, Product $product)
{
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500'
    ]);

    // Cek apakah user pernah membeli produk ini
    $hasPurchased = Order::where('user_id', auth()->id())
        ->whereHas('items', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })
        ->exists();

    if (!$hasPurchased) {
        return back()->with('error', 'Anda harus membeli produk ini terlebih dahulu untuk memberikan ulasan.');
    }

    // Hapus review lama jika ada
    Review::where('user_id', auth()->id())
        ->where('product_id', $product->id)
        ->delete();

    // Buat review baru
    $review = Review::create([
        'user_id' => auth()->id(),
        'product_id' => $product->id,
        'rating' => $validated['rating'],
        'comment' => $validated['comment'] ?? null
    ]);

    // Update rating produk
    $this->updateProductRating($product);

    return back()->with('success', 'Ulasan berhasil ditambahkan');
}

private function updateProductRating(Product $product)
{
    $avgRating = Review::where('product_id', $product->id)
        ->avg('rating');

    $product->update([
        'rating' => round($avgRating, 1)
    ]);
}

}