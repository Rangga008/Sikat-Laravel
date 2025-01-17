<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Penjualan
        </h2>
    </x-slot>

    <div 
        x-data="{ 
            exportType: '',
            isModalOpen: false,
            openExportModal(type) {
                this.exportType = type;
                this.isModalOpen = true;
            },
            closeModal() {
                this.isModalOpen = false;
            }
        }" 
        class="py-12"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Filter Periode --}}
                    <form method="GET" action="{{ route('sales.reports') }}" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700">Periode</label>
                                <select name="period" class="form-control w-full">
                                    <option value="">Pilih Periode</option>
                                    <option value="today">Hari Ini</option>
                                    <option value="this_week">Minggu Ini</option>
                                    <option value="this_month">Bulan Ini</option>
                                    <option value="last_month">Bulan Lalu</option>
                                    <option value="this_year">Tahun Ini</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control w-full">
                            </div>
                            <div>
                                <label class="block text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control w-full">
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                            
                            {{-- Tombol Export --}}
                            <button 
                                type="button" 
                                @click="openExportModal('pdf')" 
                                class="btn btn-danger"
                            >
                                Ekspor PDF
                            </button>
                            <button 
                                type="button" 
                                @click="openExportModal('excel')" 
                                class="btn btn-success"
                            >
                                Ekspor Excel
                            </button>
                        </div>
                    </form>

                    {{-- Ringkasan --}}
                    <div class="mb-4 bg-gray-100 p-4 rounded">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Total Penjualan</p>
                                <h3 class="text-xl font-bold text-green-600">
                                    Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                            <div>
                                <p class="text-gray-600">Total Item Terjual</p>
                                <h3 class="text-xl font-bold text-blue-600">
                                    {{ $totalItems ?? 0 }} Item
                                </h3>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Laporan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">ID Pesanan</th>
                                    <th class="px-4 py-2 text-left">Produk</th>
                                    <th class="px-4 py-2 text-left">Pelanggan</th>
                                    <th class="px-4 py-2 text-left">Tanggal</th>
                                    <th class="px-4 py-2 text-left">Total Penjualan</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $order->id ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">
                                            @if($order->seller_items)
                                                @foreach($order->seller_items as $item)
                                                    {{ $item->product->name ?? 'N/A' }}
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $order->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">
                                            {{ $order->created_at ? $order->created_at->format('d-m-Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 font-bold text-green-600">
                                            Rp {{ number_format($order->seller_total ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="
                                                @if($order->status == 'completed') text-green-600
                                                @elseif($order->status == 'pending') text-yellow-600
                                                @elseif($order->status == 'processing') text-blue-600
                                                @else text-red-600
                                                @endif
                                            ">
                                                {{ ucfirst($order->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-gray-500">
                                            Tidak ada data penjualan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Export --}}
        <div 
            x-show="isModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto"
        >
            <div 
                class="fixed inset-0 bg-black opacity-50"
                @click="closeModal"
            ></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h2 class="text-xl font-semibold mb-4">Pilih Rentang Tanggal Ekspor</h2>
                
                <form 
                    method="GET" 
                    action="{{ route('sales.reports') }}"
                >
                <input 
                type="hidden" 
                name="export" 
                :value="exportType"
            >
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                <input 
                    type="date" 
                    name="start_date" 
                    required 
                    class="w-full px-3 py-2 border rounded"
                >
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai</label>
                <input 
                    type="date" 
                    name="end_date" 
                    required 
                    class="w-full px-3 py-2 border rounded"
                >
            </div>
            
            <div class="flex justify-end space-x-2">
                <button 
                    type="button" 
                    @click="closeModal" 
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Ekspor
                </button>
            </div>
        </form>
    </div>
</div>
</div>

@push('scripts')
<style>
[x-cloak] { 
    display: none !important; 
}
</style>
@endpush
</x-app-layout>