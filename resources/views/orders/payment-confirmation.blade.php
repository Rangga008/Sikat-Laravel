<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Konfirmasi Pembayaran') }}
            </h2>
            <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Detail Pesanan</h3>
                        <p>ID Pesanan: #{{ $order->id }}</p>
                        <p>Total Pembayaran: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Informasi Pembayaran</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p><strong>Bank:</strong> BCA</p>
                            <p><strong>Nomor Rekening:</strong> 1234567890</p>
                            <p><strong>Nama Pemilik:</strong> Nama Toko Anda</p>
                        </div>
                    </div>

                    <form 
                        action="{{ route('orders.confirm-payment', $order) }}" 
                        method="POST" 
                        enctype="multipart/form-data"
                        class="space-y-4"
                    >
                        @csrf
                        
                        <div>
                            <label for="bank_name" class="block mb-2 text-sm font-bold">Nama Bank Pengirim</label>
                            <input 
                                type="text" 
                                name="bank_name" 
                                id="bank_name" 
                                class="w-full px-3 py-2 border rounded" 
                                required
                                value="{{ old('bank_name') }}"
                            >
                            @error('bank_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_name" class="block mb-2 text-sm font-bold">Nama Pemilik Rekening</label>
                            <input 
                                type="text" 
                                name="account_name" 
                                id="account_name" 
                                class="w-full px-3 py-2 border rounded" 
                                required
                                value="{{ old('account_name') }}"
                            >
                            @error('account_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="proof" class="block mb-2 text-sm font-bold">Bukti Transfer</label>
                            <input 
                                type="file" 
                                name="proof" 
                                id="proof" 
                                accept="image/*" 
                                class="w-full px-3 py-2 border rounded" 
                                required
                            >
                            @error('proof')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <div id="image-preview" class="mt-4"></div>
                        </div>

                        <div class="flex space-x-4">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 0 1 18 0z" />
                                </svg>
                                Kirim Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>