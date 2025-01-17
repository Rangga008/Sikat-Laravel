<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        table, th, td { 
            border: 1px solid #ddd; 
            padding: 6px; 
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>Periode: {{ $periodText }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Penjualan:</strong> Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        <p><strong>Total Item Terjual:</strong> {{ $totalItems }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Produk</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total Penjualan Produk</th>
                <th>Status Pembayaran</th>
                <th>Status Pesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id ?? 'N/A' }}</td>
                    <td>
                        @if($order->seller_items)
                            @foreach($order->seller_items as $item)
                                {{ $item->product->name ?? 'N/A' }}
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ optional($order->user)->name ?? 'N/A' }}</td>
                    <td>{{ $order->created_at ? $order->created_at->format('d-m-Y H:i') : 'N/A' }}</td>
                    <td>Rp {{ number_format($order->seller_total ?? 0, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->payment_status ?? 'N/A') }}</td>
                    <td>{{ ucfirst($order->status ?? 'N/A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>