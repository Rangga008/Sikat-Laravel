<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orders;
    protected $startDate;
    protected $endDate;

    public function __construct($orders, $startDate = null, $endDate = null)
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        $headings = [
            'ID Pesanan',
            'Produk',
            'Pelanggan',
            'Tanggal',
            'Total Penjualan',
            'Status Pembayaran',
            'Status Pesanan'
        ];

        // Tambahkan periode jika ada
        if ($this->startDate && $this->endDate) {
            $headings[] = 'Periode';
        }

        return $headings;
    }

    public function map($order): array
    {
        // Ambil nama produk
        $products = $order->seller_items
            ->pluck('product.name')
            ->unique()
            ->implode(', ');

        $data = [
            $order->id,
            $products,
            optional($order->user)->name ?? 'N/A',
            $order->created_at ? $order->created_at->format('d-m-Y H:i') : 'N/A',
            $order->seller_total ?? 0,
            ucfirst($order->payment_status ?? 'N/A'),
            ucfirst($order->status ?? 'N/A')
        ];

        // Tambahkan periode jika ada
        if ($this->startDate && $this->endDate) {
            $data[] = $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y');
        }

        return $data;
    }
}