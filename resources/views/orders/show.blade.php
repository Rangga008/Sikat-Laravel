<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detail Pesanan #{{ $order->id }}
            </h2>
            <div class="flex items-center space-x-2">
                @if($order->status === 'completed')
                    <a href="{{ route('orders.invoice', $order) }}" 
                       class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Download Invoice
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Order Summary --}}
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Informasi Pesanan</h3>
                            <div class="space-y-2">
                                <p><strong>Status:</strong> 
                                    <span class="
                                        {{ $order->status === 'completed' ? 'text-green-600' : 
                                           ($order->status === 'processing' ? 'text-blue-600' : 
                                           ($order->status === 'pending' ? 'text-yellow-600' : 'text-red-600')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                                <p><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($order->payment_method) }}</p>
                                <p><strong>Status Pembayaran:</strong> 
                                    <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Informasi Pengiriman</h3>
                            <div class="space-y-2">
                                <p><strong>Alamat:</strong> {{ $order->address }}</p>
                                <p><strong>Telepon:</strong> {{ $order->phone }}</p>
                                @if($order->notes)
                                    <p><strong>Catatan:</strong> {{ $order->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                

                {{-- Di halaman detail pesanan --}}
                @if($order->payment_method == 'transfer' && $order->payment_status == 'unpaid')
                <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                    <h3 class="text-xl font-semibold mb-4">Upload Bukti Pembayaran</h3>
                    
                    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-4">
                        <p class="font-bold">Informasi Pembayaran</p>
                        <p>Total Pembayaran: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                        <p>Metode Pembayaran: Transfer Bank</p>
                    </div>
            
                    <form action="{{ route('orders.upload-payment-proof', $order) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2">Nama Bank</label>
                                <input type="text" 
                                       name="bank_name" 
                                       class="w-full border rounded p-2" 
                                       required 
                                       placeholder="Contoh: BCA, Mandiri, dll">
                            </div>
                            
                            <div>
                                <label class="block mb-2">Nama Pemilik Rekening</label>
                                <input type="text" 
                                       name="account_name" 
                                       class="w-full border rounded p-2" 
                                       required 
                                       placeholder="Nama sesuai rekening">
                            </div>
                            
                            <div>
                                <label class="block mb-2">Tanggal Transfer</label>
                                <input type="date" 
                                       name="transfer_date" 
                                       class="w-full border rounded p-2" 
                                       required>
                            </div>
                            
                            <div>
                                <label class="block mb-2">Bukti Transfer (Maks 2MB)</label>
                                <input type="file" 
                                       name="payment_proof" 
                                       accept="image/*" 
                                       class="w-full border rounded p-2" 
                                       required>
                            </div>
                        </div>
            
                        <button type="submit" class="mt-4 w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                            Upload Bukti Pembayaran
                        </button>
                    </form>
                </div>
            @endif
            {{-- Bagian Item Pesanan --}}
{{-- Bagian Item Pesanan --}}
<div class="p-6 bg-white border-t border-gray-200">
    <h3 class="text-lg font-semibold mb-4">Item Pesanan</h3>
    @foreach($order->items as $item)
        <div class="flex items-center justify-between mb-4 pb-4 border-b">
            <div class="flex items-center space-x-4">
                {{-- Foto Produk --}}
                <div class="w-20 h-20 flex-shrink-0">
                    <img 
                        src="{{ $item->product->image_url ?? asset('images/default-product.png') }}" 
                        alt="{{ $item->product->name }}"
                        class="w-full h-full object-cover rounded-lg"
                    >
                </div>

                <div>
                    <p class="font-semibold">{{ $item->product->name }}</p>
                    <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
                    <p class="text-gray-600">Harga: Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    <p class="text-gray-600">Subtotal: Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div>
                @php
                    \Log::info('Review Debug', [
                        'item_id' => $item->id,
                        'order_status' => $order->status,
                        'has_review' => $item->has_review,
                        'can_be_reviewed' => $item->can_be_reviewed
                    ]);
                @endphp

                @if($order->status === 'completed')
                    @if($item->can_be_reviewed)
                        <button 
                            onclick="showReviewModal({{ $item->id }})"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        >
                            Tulis Review
                        </button>
                    @elseif($item->has_review)
                        <span class="text-green-600">Sudah direview</span>
                    @endif
                @endif
            </div>
        </div>
    @endforeach

    {{-- Total Pembayaran --}}
    <div class="mt-6 text-right">
        <p class="text-lg font-semibold">
            Total Pembayaran: 
            <span class="text-blue-600">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
        </p>
    </div>
</div>

    {{-- Tombol Lacak Pesanan untuk semua metode pembayaran yang sudah dibayar --}}
    @if($order->status === 'processing' && $order->payment_status === 'paid')
        <div class="flex space-x-4">
            <a 
                href="{{ route('orders.tracking', $order) }}" 
                class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 text-center flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lacak Pesanan
            </a>
        </div>
    @endif

    {{-- Konfirmasi Pengiriman --}}
    @if($order->status === 'processing' && !$order->delivered_at)
        <form action="{{ route('orders.confirm-delivery', $order) }}" method="POST">
            @csrf
            <button 
                type="submit" 
                class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600"
            >
                Konfirmasi Pengiriman
            </button>
        </form>
    @endif

    {{-- COD payment conditions --}}
    @if($order->status === 'pending' && $order->payment_method === 'cod')
        <div class="flex space-x-4">
            <form action="{{ route('orders.confirm-cod-payment', $order) }}" method="POST" class="w-1/2">
                @csrf
                <button 
                    type="submit" 
                    class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600"
                >
                    Konfirmasi Pembayaran
                </button> 
            </form>

            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="w-1/2">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <button 
                    type="submit" 
                    class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600"
                >
                    Batalkan Order
                </button>
            </form>
        </div>
    @endif
</div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">Tulis Ulasan</h3>
                <form id="reviewForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-2">Rating</label>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" class="hidden" required>
                                <label for="rating{{ $i }}" class="cursor-pointer text-2xl text-yellow-500 px-1">☆</label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="comment" class="block mb-2">Komentar (Opsional)</label>
                        <textarea name="comment" id="comment" rows="4" class="w-full border rounded p-2" maxlength="500"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="hideReviewModal()" class="px-4 py-2 text-gray-600 mr-2">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Kirim Ulasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showReviewModal(orderItemId) {
            const modal = document.getElementById('reviewModal');
            const form = document.getElementById('reviewForm');
            form.action = `/order-items/${orderItemId}/review`;
            modal.classList.remove('hidden');
        }

        function hideReviewModal() {
            const modal = document.getElementById('reviewModal');
            modal.classList.add('hidden');
        }

        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XML HttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim review');
            });
        });

        // Star rating functionality
        const stars = document.querySelectorAll('[id^="rating"]');
        stars.forEach((star, index) => {
            star.nextElementSibling.addEventListener('click', () => {
                stars.forEach((s, i) => {
                    s.nextElementSibling.textContent = i <= index ? '★' : '☆';
                    if (i <= index) {
                        s.checked = true;
                    }
                });
            });
        });
    </script>
</x-app-layout>