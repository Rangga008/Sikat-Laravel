<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($orders->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">You haven't placed any orders yet.</p>
                            <a href="{{ route('dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Browse Products
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-6 space-y-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold">
                                                Order #{{ $order->orderNumber }} - {{ $order->user->name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded-full text-sm 
                                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'completed') bg-green-100 text-green-800
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4">
                                        <div class="space-y-2">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center space-x-4">
                                                        @if($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded">
                                                        @else
                                                            <div class="w-16 h-16 bg-gray-200 rounded"></div>
                                                        @endif
                                                        <div>
                                                            <h4 class="font-medium">{{ $item->product->name }}</h4>
                                                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="font-medium">Rp {{ number_format($item->subtotal) }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="border-t pt-4 flex justify-between items-center">
                                        <div class="text-sm">
                                            <p><span class="font-medium">Shipping Address:</span> {{ $order->address }}</p>
                                            <p><span class="font-medium">Phone:</span> {{ $order->phone }}</p>
                                            @if($order->notes)
                                                <p><span class="font-medium">Notes:</span> {{ $order->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Total Amount:</p>
                                            <p class="text-xl font-bold">Rp {{ number_format($order->total_amount) }}</p>
                                            <a href="{{ route('orders.show', $order) }}" class="mt-2 inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>