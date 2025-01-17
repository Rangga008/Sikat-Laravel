<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();
            
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Cek apakah produk sudah ada di cart
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity jika produk sudah ada
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity']
            ]);
        } else {
            // Buat cart item baru
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity']
            ]);
        }

        // Redirect ke halaman cart dengan pesan sukses
        return redirect()->route('cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Pastikan cart milik user yang sedang login
        if ($cart->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan mengubah cart ini');
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui');
    }

    public function destroy(Cart $cart)
    {
        // Pastikan cart milik user yang sedang login
        if ($cart->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus cart ini');
        }

        $cart->delete();
        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang');
    }
}