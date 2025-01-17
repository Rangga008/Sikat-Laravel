<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Gambar Produk --}}
                <div>
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-96 object-cover rounded-lg shadow-lg">
                    @endif
                </div>

                {{-- Informasi Produk --}}
                <div>
                    <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
                    
                    <div class="flex items-center mb-4">
                        <span class="text-2xl font-semibold text-gray-900 mr-4">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <div class="flex items-center">
                            <span class="mr-1 text-yellow-500">★</span>
                            <span>{{ $product->rating ?? 0 }}</span>
                            <span class="mx-2">|</span>
                            <span>{{ $product->sold_count }} terjual</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Deskripsi Produk</h3>
                        <p class="text-gray-600">{{ $product->description }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Ketersediaan</h3>
                        <p class="text-gray-600">Stok: {{ $product->stock }} tersedia</p>
                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <div>
                                <label for="quantity" class="block mb-2">Jumlah</label>
                                <input 
                                    type="number" 
                                    name="quantity" 
                                    id="quantity"
                                    value="1" 
                                    min="1" 
                                    max="{{ $product->stock }}"
                                    class="w-24 px-3 py-2 border rounded"
                                >
                            </div>
                            <div>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                >
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Produk Terkait --}}
            @if($relatedProducts->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6">Produk Terkait</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                @if($relatedProduct->image)
                                    <img 
                                        src="{{ Storage::url($relatedProduct->image) }}" 
                                        alt="{{ $relatedProduct->name }}" 
                                        class="w-full h-48 object-cover"
                                    >
                                @endif
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">
                                        <a href="{{ route('products.show', $relatedProduct) }}" class="hover:text-blue-500">
                                            {{ $relatedProduct->name }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600">
                                        Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>