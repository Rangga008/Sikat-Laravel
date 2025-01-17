<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .company-info, .order-info {
            width: 45%;
        }
        .invoice-box table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-box table th, 
        .invoice-box table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            vertical-align: middle;
            margin-right: 10px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            vertical-align: middle;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-header">
            <div class="company-info">
                <h2>{{ config('app.name') }}</h2>
                <p>Alamat Perusahaan</p>
                <p>Telepon: {{ config('app.phone', '-') }}</p>
                <p>Email: {{ config('app.email', '-') }}</p>
            </div>
            <div class="order-info">
                <h1>INVOICE</h1>
                <p><strong>Nomor Order:</strong> #{{ $order->id }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <h3>Detail Pelanggan</h3>
        <table>
            <tr>
                <td><strong>Nama</strong></td>
                <td>{{ $order->user->name }}</td>
            </tr>
            <tr>
                <td><strong>Alamat Pengiriman</strong></td>
                <td>{{ $order->address }}</td>
            </tr>
            <tr>
                <td><strong>Telepon</strong></td>
                <td>{{ $order->phone }}</td>
            </tr>
        </table>

        <h3>Detail Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Gambar</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>
                            @php
                                // Debug informasi gambar
                                \Log::info('Invoice Image Debug', [
                                    'product_id' => $item->product_id,
                                    'absolute_path' => $item->product->absolute_image_path ?? 'No path',
                                    'file_exists' => $item->product->absolute_image_path ? file_exists($item->product->absolute_image_path) : false
                                ]);
                            @endphp

                            @if(isset($item->product->absolute_image_path) && file_exists($item->product->absolute_image_path))
                                <img 
                                    src="{{ $item->product->absolute_image_path }}" 
                                    alt="{{ $item->product->name }}" 
                                    class="product-image"
                                >
                            @else
                                <span>Tidak ada gambar</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align:right;">Total Pesanan</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="display:flex; justify-content:space-between; margin-top:20px;">
            <div>
                <h4>Metode Pembayaran</h4>
                <p>{{ ucfirst($order->payment_method) }}</p>
            </div>
            <div>
                <h4>Status Pembayaran</h4>
                <p>{{ ucfirst($order->payment_status) }}</p>
            </div>
        </div>

        <div style="margin-top:30px; text-align:center; font-size:10px; color:#888;">
            <p>Terima kasih telah berbelanja</p>
            <p>{{ config('app.name') }} - Invoice @K</p>
        </div>
    </div>
</body>
</html>