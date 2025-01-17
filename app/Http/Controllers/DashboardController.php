<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('stock', '>', 0)
            ->with('category');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->orderBy('sold_count', 'desc'); // Ensure this column exists
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // Paginate results
        $products = $query->paginate(12);

        // Get categories for filtering
        $categories = Category::all();

        return view('dashboard', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->category,
            'selectedSort' => $request->sort,
            'searchQuery' => $request->search
        ]);
    }
}