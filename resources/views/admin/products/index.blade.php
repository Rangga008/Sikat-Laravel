<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('admin.products.create') }}" 
               class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Mobile View --}}
                    <div class="block md:hidden">
                        @foreach ($products as $product)
                            <div class="bg-white shadow rounded-lg mb-4 p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form class="inline" 
                                              action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="font-semibold">Category:</span>
                                        @if($product->category)
                                            <span class="px-2 py-1 text-xs text-gray-600 bg-gray-200 rounded-full">
                                                {{ $product->category->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded-full">
                                                No Category
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-semibold">Price:</span>
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Stock:</span>
                                        @if($product->stock <= 5)
                                            <span class="text-red-500">{{ $product->stock }}</span>
                                        @else
                                            {{ $product->stock }}
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-semibold">Rating:</span>
                                        <div class="flex items-center">
                                            <span class="mr-1 text-yellow-500">★</span>
                                            {{ number_format($product->rating ?? 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Sold:</span>
                                        {{ $product->sold_count }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop View --}}
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left">Name</th>
                                    <th class="px-6 py-3 text-left">Category</th>
                                    <th class="px-6 py-3 text-left">Price</th>
                                    <th class="px-6 py-3 text-left">Stock</th>
                                    <th class="px-6 py-3 text-left">Rating</th>
                                    <th class="px-6 py-3 text-left">Sold</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($products as $product)
                                <tr>
                                    <td class="px-6 py-4">{{ $product->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($product->category)
                                            <span class="px-2 py-1 text-xs text-gray-600 bg-gray-200 rounded-full">
                                                {{ $product->category->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded-full">
                                                No Category
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        @if($product->stock <= 5)
                                            <span class="text-red-500">{{ $product->stock }}</span @else
                                            {{ $product->stock }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="mr-1 text-yellow-500">★</span>
                                            {{ number_format($product->rating ?? 0, 1) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $product->sold_count }}</td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            Edit
                                        </a>
                                        <form class="inline" 
                                              action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>