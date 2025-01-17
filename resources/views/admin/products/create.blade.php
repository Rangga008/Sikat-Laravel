<!-- resources/views/admin/products/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nama Produk</label>
                    <input type="text" name="name" id="name" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           value="{{ old('name') }}" required>
                </div>

                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700">Kategori</label>
                    <select name="category_id" id="category_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Harga</label>
                    <input type="number" name="price" id="price" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           value="{{ old('price') }}" required>
                </div>

                <div class="mb-4">
                    <label for="stock" class="block text-gray-700">Stok</label>
                    <input type="number" name="stock" id="stock" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           value="{{ old('stock') }}" required>
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Gambar Produk</label>
                    <input type="file" name="image" id="image" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Simpan Produk
                </button>
            </form>
        </div>
    </div>
</x-app-layout>