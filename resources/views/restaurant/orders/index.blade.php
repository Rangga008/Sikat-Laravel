<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Pesanan Menunggu Konfirmasi Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Pelanggan</th>
                                    <th class="px-4 py-2 text-left">Produk</th>
                                    <th class="px-4 py-2 text-left">Metode Pembayaran</th>
                                    <th class="px-4 py-2 text-left">Total Pembayaran</th>
                                    <th class="px-4 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 flex items-center space-x-3">
                                            <img 
                                                src="{{ $order->user->profile_photo_url ?? asset('default-avatar.png') }}" 
                                                alt="{{ $order->user->name }}" 
                                                class="w-10 h-10 rounded-full"
                                            >
                                            <div>
                                                <div class="font-semibold">{{ $order->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center space-x-3">
                                                <img 
                                                    src="{{ $order->items->first()->product->image_url ?? asset('default-product.png') }}" 
                                                    alt="{{ $order->items->first()->product->name }}" 
                                                    class="w-10 h-10 rounded"
                                                >
                                                <div>
                                                    <div>{{ $order->items->first()->product->name }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $order->items->count() > 1 ? '+' . ($order->items->count() - 1) . ' produk lainnya' : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="
                                                @if($order->payment_method == 'transfer') bg-blue-100 text-blue-800
                                                @elseif($order->payment_method == 'cod') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                                px-2 py-1 rounded text-sm
                                            ">
                                                {{ strtoupper($order->payment_method) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('restaurant.orders.show', $order) }}" 
                                               class="text-blue-500 hover:text-blue-700 mr-2">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                            Tidak ada pesanan menunggu konfirmasi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>