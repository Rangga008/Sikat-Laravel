<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Payment Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Order Details</h3>
                        <p>Order ID: #{{ $order->id }}</p>
                        <p>Total Amount: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Bank Transfer Information</h3>
                        <p>Bank: BCA</p>
                        <p>Account Number: 1234567890</p>
                        <p>Account Name: Your Store Name</p>
                    </div>

                    <form action="{{ route('orders.confirm-payment', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="proof" class="block mb-2 text-sm font-bold text-gray-700">Upload Payment Proof</label>
                            <input type="file" name="proof" id="proof" class="w-full px-3 py-2 border rounded" required>
                            @error('proof')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                            Submit Payment Proof
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>