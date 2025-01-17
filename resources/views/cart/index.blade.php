<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Shopping Cart') }}
            </h2>
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="text-gray-600">{{ $cartItems->count() }} Item(s)</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if($cartItems->isEmpty())
                {{-- Konten keranjang kosong --}}
                <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-gray-700">Your cart is empty</h3>
                    <p class="mt-2 text-gray-500">Looks like you haven't added any items to your cart yet.</p>
                    <a href="{{ route('dashboard') }}" class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Continue Shopping
                    </a>
                </div>
            @else
                {{-- Versi Desktop --}}
                <div class="hidden md:grid md:grid-cols-3 gap-6">
                    {{-- Cart Items Column --}}
                    <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left">Product</th>
                                    <th class="px-6 py-3 text-left">Price</th>
                                    <th class="px-6 py-3 text-left">Quantity</th>
                                    <th class="px-6 py-3 text-left">Subtotal</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 flex items-center space-x-4">
                                            @if($item->product->image)
                                                <img 
                                                    src="{{ asset('storage/' . $item->product->image) }}" 
                                                    alt="{{ $item->product->name }}" 
                                                    class="w-16 h-16 object-cover rounded"
                                                >
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                                    No Image
                                                </div>
                                            @endif
                                            <span>{{ $item->product->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <form action="{{ route('cart.update', $item) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-20 px-2 py-1 border rounded">
                                                <button type="submit" class="px-2 py-1 text-sm text-white bg-blue-500 rounded">Update</button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4">
                                            Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <form action="{{ route('cart.destroy', $item) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Order Summary Column --}}
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Order Summary</h3>
                        <div class="mt-4">
                            <h4 class="text-lg font-semibold">Total: Rp {{ number_format($total, 0, ',', '.') }}</h4>
                            <form action="{{ route('orders.store') }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-4">
                                    <label for="address" class="block mb-2 text-sm font-bold text-gray-700">Delivery Address</label>
                                    <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border rounded" required></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="block mb-2 text-sm font-bold text-gray-700">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="w-full px-3 py-2 border rounded" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="payment_method" class="block mb-2 text-sm font-bold text-gray-700">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border rounded" required>
                                        <option value="transfer">Bank Transfer</option>
                                        <option value="cod">Cash on Delivery</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block mb-2 text-sm font-bold text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border rounded"></textarea>
                                </div>

                                <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                                    Place Order
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Versi Mobile --}}
                <div class="md:hidden">
                    <div class="bg-white shadow-lg rounded-lg p-4">
                        <h2 class="text-lg font-semibold mb-4">Cart Items</h2>
                        @foreach($cartItems as $item)
                            <div class="flex items-center bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="flex-shrink-0 mr-3">
                                    @if($item->product->image)
                                        <img 
                                            src="{{ asset('storage/' . $item->product->image) }}" 
                                            alt="{{ $item->product->name }}" 
                                            class="w-16 h-16 object-cover rounded"
                                        >
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                            No Image
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold">{{ $item->product->name }}</h3>
                                    <p class="text-xs text-gray-600">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center mt-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 px-2 py-1 border rounded mr-2">
                                        <button type="submit" class="text-blue-500 hover:text-blue-700">Update</button>
                                    </form>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-semibold">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white shadow-lg rounded-lg p-4 mt-4">
                        <h3 class="text-lg font-semibold">Order Summary</h3>
                        <h4 class="text-lg font-semibold">Total: Rp {{ number_format($total, 0, ',', '.') }}</h4>
                        <form action="{{ route('orders.store') }}" method="POST" class="mt-4">
                            @csrf
                            <div class="mb-4">
                                <label for="address" class="block mb-2 text-sm font-bold text-gray-700">Delivery Address</label>
                                <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border rounded" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block mb-2 text-sm font-bold text-gray-700">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="w-full px-3 py-2 border rounded" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="payment_method" class="block mb-2 text-sm font-bold text-gray-700">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border rounded" required>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block mb-2 text-sm font-bold text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border rounded"></textarea>
                            </div>

                            <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>