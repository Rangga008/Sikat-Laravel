<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block mb-2 text-sm font-bold text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded shadow appearance-none" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 text-sm font-bold text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded shadow appearance-none" required>{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block mb-2 text-sm font-bold text-gray-700">Price</label>
                            <input type="number" name="price" id="price" class="w-full px-3 py-2 border rounded shadow appearance-none" value="{{ old('price', $product->price) }}" required>
                            @error('price')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock" class="block mb-2 text-sm font-bold text-gray-700">Stock</label>
                            <input type="number" name="stock" id="stock" class="w-full px-3 py-2 border rounded shadow appearance-none" value="{{ old('stock', $product->stock) }}" required>
                            @error('stock')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="block mb-2 text-sm font-bold text-gray-700">Kategori</label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded shadow appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($product->image)
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Current Image</label>
                            <img src="{{ Storage::url($product->image) }}" alt="Current product image" class="w-32 h-32 object-cover">
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="image" class="block mb-2 text-sm font-bold text-gray-700">New Image</label>
                            <input type="file" name="image" id="image" class="w-full px-3 py-2 border rounded shadow appearance-none">
                            @error('image')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                                Update Product
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>