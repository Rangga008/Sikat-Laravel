<!-- resources/views/orders/cod-payment-details.blade.php -->
<x-app-layout>
    <div class="container mx-auto p-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Konfirmasi Pembayaran COD</h2>
            
            <div class="mb-4">
                <p><strong>Nomor Order:</strong> {{ $order->id }}</p>
                <p><strong>Total Pembayaran:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <p><strong>Status Saat Ini:</strong> {{ $order->status }}</p>
            </div>

            <form action="{{ route('orders.confirm-cod-payment', $order->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-2">Konfirmasi Pembayaran</label>
                    <div class="flex items-center space-x-4">
                        <button 
                            type="submit" 
                            name="confirm_payment" 
                            value="1"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                        >
                            Pembayaran Diterima
                        </button>
                        <button 
                            type="submit" 
                            name="confirm_payment" 
                            value="0"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                        >
                            Batalkan Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>