<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Lacak Pesanan #{{ $order->id }}
            </h2>
            <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Status Pesanan</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-{{ 
                            $order->status === 'pending' ? 'yellow' : 
                            ($order->status === 'processing' ? 'blue' : 
                            ($order->status === 'completed' ? 'green' : 'red')) 
                        }}-500 rounded-full mr-4"></div>
                        <div>
                            <h4 class="font-semibold">{{ ucfirst($order->status) }}</h4>
                            <p class="text-gray-600">
                                @switch($order->status)
                                    @case('pending')
                                        Pesanan sedang diproses
                                        @break
                                    @case('processing')
                                        Pesanan dalam perjalanan
                                        @break
                                    @case('completed')
                                        Pesanan telah diterima
                                        @break
                                    @default
                                        Pesanan dibatalkan
                                @endswitch
                            </p>
                        </div>
                    </div>

                    @if($order->payment_method === 'transfer' && $order->payment_proof)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Bukti Pembayaran</h4>
                            <img 
                                src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Pembayaran" 
                                class="w-full h-auto rounded shadow"
                            >
                        </div>
                    @endif

                    <div class="mt-4">
                        <h4 class="font-semibold mb-2">Informasi Pengiriman</h4>
                        <p><strong>Alamat:</strong> {{ $order->address }}</p>
                        <p><strong>Telepon:</strong> {{ $order->phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>