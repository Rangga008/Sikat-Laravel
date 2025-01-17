<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        @if($period)
            <p>Periode: {{ ucfirst(str_replace('_', ' ', $period)) }}</p>
        @endif
        @if($startDate && $endDate)
            <p>Dari {{ $startDate }} sampai {{ $endDate }}</p>
        @endif
        <p>Total Penjualan: Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        <p>Total Item Terjual: {{ $totalItems }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total Penjualan</th>
                <th>Jumlah Item</th>
                <th>Status Pembayaran</th>
                <th>Status Pesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i:s') }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $order->items->sum('quantity') }}</td>
                    <td>{{ ucfirst($order->payment_status) }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>