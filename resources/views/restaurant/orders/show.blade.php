<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Pesanan #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Informasi Pelanggan --}}
                    <div class="mb-6 flex items-center space-x-4">
                        <img 
                            src="{{ $order->user->profile_photo_url ?? asset('default-avatar.png') }}" 
                            alt="{{ $order->user->name }}" 
                            class="w-20 h-20 rounded-full"
                        >
                        <div>
                            <h3 class="text-lg font-semibold">{{ $order->user->name }}</h3>
                            <p class="text-gray-600">{{ $order->user->email }}</p>
                            <p class="text-gray-600">{{ $order->phone ?? 'Tidak ada nomor telepon' }}</p>
                        </div>
                    </div>

                    {{-- Informasi Pembayaran --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi Pembayaran</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <strong>Metode Pembayaran:</strong> {{ ucfirst($order->payment_method) }}
                            </div>
                            <div>
                                <strong>Total Pembayaran:</strong> 
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        @if($order->payment_method == 'transfer' && $order->payment_status == 'pending')
                            <div class="mt-4 flex items-center space-x-4">
                                @if($order->payment_proof)
                                    <img 
                                        src="{{ Storage::url($order->payment_proof) }}" 
                                        alt="Bukti Pembayaran" 
                                        class="max-w-xs rounded-lg"
                                    >
                                    <a 
                                        href="{{ route('download.payment.proof', $order) }}" 
                                        class="btn btn-primary"
                                    >
                                        Download Bukti Pembayaran
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Detail Pesanan --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Detail Pesanan</h3>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center space-x-4 border-b pb-2">
                                    <img 
                                        src="{{ $item->product->image_url ?? asset('default-product.png') }}" 
                                        alt="{{ $item->product->name }}" 
                                        class="w-16 h-16 rounded"
                                    >
                                    <div class="flex-grow">
                                        <div class="font-semibold">{{ $item->product->name }}</div>
                                        <div class="text-gray-600">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}
                                        </div>
                                    </div>
                                    <div class="font-bold">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Konfirmasi Pembayaran --}}
                    @if($order->payment_method == 'transfer' && $order->payment_status == 'pending')
                        <div class="mt-6 bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Konfirmasi Pembayaran</h3>
                            <form 
                                action="{{ route('restaurant.orders.confirm-payment', $order) }}" 
                                method="POST" 
                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                            >
                                @csrf
                                <div>
                                    <label class="block mb-2 font-medium">Keputusan Pembayaran</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center space-x-2">
                                            <input 
                                                type="radio" 
                                                name="status" 
                                                value="approved" 
                                                class="form-radio text-green-500"
                                                required
                                            >
                                            <span class="text-green-600">Terima Pembayaran</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input 
                                                type="radio" 
                                                name="status" 
                                                value="rejected" 
                                                class="form-radio text-red-500"
                                                required
                                            >
                                            <span class="text-red-600">Tolak Pembayaran</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 font-medium">Catatan (Opsional)</label>
                                    <textarea 
                                        name="notes" 
                                        class="w-full border rounded p-2" 
                                        rows="3" 
                                        placeholder="Berikan catatan jika diperlukan"
                                    ></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <button 
                                        type="submit" 
                                        class="w-full btn btn-primary py-3 rounded-lg"
                                    >
                                        Konfirmasi Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Riwayat Status Pesanan --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Riwayat Status Pesanan</h3>
                        <div class="space-y-2">
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="flex justify-between">
                                    <span class="font-medium">Pesanan Dibuat</span>
                                    <span class="text-gray-600">
                                        {{ $order->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            @if($order->payment_status == 'paid')
                                <div class="bg-green-50 p-3 rounded">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-green-700">Pembayaran Dikonfirmasi</span>
                                        <span class="text-green-600">
                                            {{ $order->updated_at->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>