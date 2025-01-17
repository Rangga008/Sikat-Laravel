<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Dashboard') }}
            </h2>
            
            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                {{-- Search Form --}}
                <form method="get" action="{{ route('dashboard') }}" class="flex items-center w-full md:w-auto">
                    <div class="relative w-full">
                        <input type="search" 
                               name="search" 
                               placeholder="Cari produk..." 
                               value="{{ request('search') }}"
                               class="w-full px-3 py-2 border rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" 
                                class="absolute right-0 top-0 bottom-0 px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- Sorting Dropdown --}}
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center w-full md:w-auto">
                    <select name="sort" 
                            onchange="this.form.submit()" 
                            class="border rounded px-2 py-2 w-full md:w-auto">
                        <option value="">Urutkan</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Search Result Info --}}
            @if(request('search'))
                <div class="mb-4 text-gray-600">
                    Hasil pencarian untuk: 
                    <span class="font-semibold">{{ request('search') }}</span>
                    @if($products->total() > 0)
                        <span class="ml-2 text-sm">({{ $products->total() }} produk ditemukan)</span>
                    @endif
                </div>
            @endif

            {{-- Kategori Filter --}}
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" 
                   class="px-3 py-1 {{ !request('category') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} rounded-full text-sm">
                    Semua Produk
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('dashboard', ['category' => $category->id, 'search' => request('search')]) }}" 
                       class="px-3 py-1 {{ request('category') == $category->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} rounded-full text-sm">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Produk Grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @forelse ($products as $product)
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <a href="{{ route('products.show', $product) }}">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="object-cover w-full h-48">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    No Image
                                </div>
                            @endif
                        </a>
                        
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-xl font-semibold">
                                    <a href="{{ route('products.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                @if($product->category)
                                    <span class="text-xs px-2 py-1 bg-gray-200 text-gray-700 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="mb-2 text-gray-600 font-bold">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            
                            <div class="flex items-center mb-2 text-sm text-gray-600">
                                <span class="mr-1 text-yellow-500">★</span>
                                <span>{{ number_format($product->rating ?? 0, 1) }}</span>
                                <span class="mx-2">|</span>
                                <span>{{ $product->sold_count }} terjual</span>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-4">
                                {{ Str::limit($product->description, 100) }}
                            </p>
                            
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           name="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->stock }}" 
                                           class="w-20 px-2 py-1 border rounded text-center">
                                    <button type="submit" 
                                            class="flex-1 px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 
                                                   {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $product->stock == 0 ? 'disabled' : '' }}>
                                        {{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600">
                            @if(request('search'))
                                Tidak ada produk yang cocok dengan pencarian "{{ request('search') }}"
                            @else
                                Tidak ada produk yang tersedia
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>