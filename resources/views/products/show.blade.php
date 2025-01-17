<x-app-layout>
    <div class="py-4 sm:py-12">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                {{-- Gambar Produk --}}
                <div>
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-48 sm:h-96 object-cover rounded-lg shadow-lg"
                        >
                    @endif
                </div>

                {{-- Informasi Produk --}}
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold mb-2 sm:mb-4">{{ $product->name }}</h1>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center mb-2 sm:mb-4">
                        <span class="text-lg sm:text-2xl font-semibold text-gray-900 mr-0 sm:mr-4 mb-2 sm:mb-0">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <div class="flex items-center">
                            <span class="mr-1 text-yellow-500">★</span>
                            <span>{{ $product->rating ?? 0 }}</span>
                            <span class="mx-2">|</span>
                            <span>{{ $product->sold_count }} terjual</span>
                        </div>
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-1 sm:mb-2">Deskripsi Produk</h3>
                        <p class="text-sm sm:text-base text-gray-600">{{ $product->description }}</p>
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-1 sm:mb-2">Ketersediaan</h3>
                        <p class="text-sm sm:text-base text-gray-600">Stok: {{ $product->stock }} tersedia</p>
                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        @csrf
                        <div>
                            <label for="quantity" class="block mb-1 text-sm">Jumlah</label>
                            <input 
                                type="number" 
                                name="quantity" 
                                id="quantity"
                                value="1" 
                                min="1" 
                                max="{{ $product->stock }}"
                                class="w-20 px-2 py-1 text-sm border rounded"
                            >
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Tambah ke Keranjang
                            </button>
                            <a 
                                href="{{ route('dashboard') }}" 
                                class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                            >
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Produk Terkait --}}
            @if($relatedProducts->count() > 0)
                <div class="mt-8 sm:mt-12">
                    <h2 class="text-lg sm:text-2xl font-bold mb-4 sm:mb-6">Produk Terkait</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                @if($relatedProduct->image)
                                    <img 
                                        src="{{ Storage::url($relatedProduct->image) }}" 
                                        alt="{{ $relatedProduct->name }}" 
                                        class="w-full h-24 sm:h-48 object-cover"
                                    >
                                @endif
                                <div class="p-2 sm:p-4">
                                    <h3 class="text-xs sm:text-lg font-semibold mb-1 sm:mb-2">
                                        <a href="{{ route('products.show', $relatedProduct) }}" class="hover:text-blue-500">
                                            {{ $relatedProduct->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs sm:text-base text-gray-600">
                                        Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ulasan Produk --}}
            <div class="mt-8 sm:mt-12">
                <h2 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6">Ulasan Produk</h2>
    
                @if($reviews->count() > 0)
                    <div class="space-y-4 sm:space-y-6">
                        @foreach($reviews as $review)
                            <div class="bg-gray-50 p-4 sm:p-6 rounded-lg">
                                <div class="flex items-center mb-2 sm:mb-4">
                                    <img 
                                        src="{{ $review->user->profile_photo_url ?? asset('default-avatar.png') }}" 
                                        alt="{{ $review->user->name }}" 
                                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full mr-2 sm:mr-4"
                                    >
                                    <div>
                                        <h4 class="text-sm sm:text-base font-semibold">{{ $review->user->name }}</h4>
                                        <div class="flex items-center">
                                            <div class="text-yellow-500 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        ★
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-gray-500 text-xs sm:text-sm">
                                                {{ $review->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($review->comment)
                                    <p class="text-xs sm:text-base text-gray-700">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm sm:text-base text-gray-500 Belum ada ulasan untuk produk ini.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>