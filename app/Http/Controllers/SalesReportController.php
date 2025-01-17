<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use Barryvdh\DomPDF\Facade\Pdf;


class SalesReportController extends Controller
{
    public function index(Request $request)
{
    // Inisialisasi default untuk startDate dan endDate
    $startDate = null;
    $endDate = null;

    // Debug: pastikan user memiliki produk
    $user = auth()->user();
    
    // Jika tidak ada produk, kembalikan view kosong
    $sellerProductIds = $user->products->pluck('id');
    
    if ($sellerProductIds->isEmpty()) {
        return view('sales.reports', [
            'orders' => collect([]),
            'totalSales' => 0,
            'totalItems' => 0,
            'message' => 'Anda belum memiliki produk yang dijual',
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    // Query order items yang mengandung produk milik penjual
    $query = OrderItem::whereIn('product_id', $sellerProductIds)
        ->with(['order.user', 'product']);

    // Filter berdasarkan periode
    if ($request->has('period')) {
        $query = $this->applyPeriodFilter($query, $request->input('period'));
    }

    // Filter berdasarkan tanggal manual dengan presisi penuh
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        
        $query->whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    // Ambil order items
    $orderItems = $query->get();

    // Dapatkan order yang memiliki produk penjual
    $orders = collect();
    $totalSales = 0;
    $totalItems = 0;

    // Kelompokkan berdasarkan order
    $processedOrderIds = [];
    $orderItems->groupBy('order_id')->each(function($items) use (&$orders, &$totalSales, &$totalItems, &$processedOrderIds) {
        // Pastikan order tidak null
        $firstItem = $items->first();
        $order = $firstItem->order ?? null;
        
        if ($order && !in_array($order->id, $processedOrderIds)) {
            // Hitung total penjualan hanya untuk produk milik penjual
            $sellerTotal = $items->sum(function($item) {
                return $item->quantity * $item->price;
            });

            // Tambahkan properti total penjualan penjual
            $order->seller_total = $sellerTotal;
            $order->seller_items = $items;
            
            // Tambahkan ke koleksi jika total penjualan > 0
            if ($sellerTotal > 0) {
                $orders->push($order);
                $totalSales += $sellerTotal;
                $totalItems += $items->sum('quantity');
                $processedOrderIds[] = $order->id;
            }
        }
    });

    // Ekspor jika diminta
    if ($request->has('export')) {
        return $this->export(
            $request->input('export'), 
            $orderItems, 
            $startDate, 
            $endDate
        );
    }

    return view('sales.reports', [
        'orders' => $orders,
        'totalSales' => $totalSales,
        'totalItems' => $totalItems,
        'startDate' => $startDate,
        'endDate' => $endDate
    ]);
}


private function applyPeriodFilter($query, $period)
{
    $now = Carbon::now();

    switch ($period) {
        case 'today':
            return $query->whereHas('order', function($q) use ($now) {
                $q->whereDate('created_at', $now->today());
            });
        
        case 'this_week':
            return $query->whereHas('order', function($q) use ($now) {
                $q->whereBetween('created_at', [
                    $now->startOfWeek(), 
                    $now->copy()->endOfWeek()
                ]);
            });
        
        case 'this_month':
            return $query->whereHas('order', function($q) use ($now) {
                $q->whereBetween('created_at', [
                    $now->startOfMonth(), 
                    $now->copy()->endOfMonth()
                ]);
            });
        
        case 'last_month':
            $lastMonth = $now->subMonth();
            return $query->whereHas('order', function($q) use ($lastMonth) {
                $q->whereBetween('created_at', [
                    $lastMonth->startOfMonth(), 
                    $lastMonth->copy()->endOfMonth()
                ]);
            });
        
        case 'this_year':
            return $query->whereHas('order', function($q) use ($now) {
                $q->whereBetween('created_at', [
                    $now->startOfYear(), 
                    $now->copy()->endOfYear()
                ]);
            });
        
        default:
            return $query;
    }
}

    // Modifikasi method export
    private function export($type, $orderItems, $startDate = null, $endDate = null)
{
    try {
        // Eager load relasi yang diperlukan
        $orderItems = $orderItems->load([
            'order.user', 
            'product'
        ]);

        // Dapatkan order yang memiliki produk penjual
        $orders = collect();
        $totalSales = 0;
        $totalItems = 0;

        // Kelompokkan berdasarkan order
        $processedOrderIds = [];
        $orderItems->groupBy('order_id')->each(function($items) use (&$orders, &$totalSales, &$totalItems, &$processedOrderIds) {
            // Pastikan order tidak null
            $firstItem = $items->first();
            $order = $firstItem->order ?? null;
            
            if ($order && !in_array($order->id, $processedOrderIds)) {
                // Hitung total penjualan hanya untuk produk milik penjual
                $sellerTotal = $items->sum(function($item) {
                    return $item->quantity * $item->price;
                });

                // Tambahkan properti total penjualan penjual
                $order->seller_total = $sellerTotal;
                $order->seller_items = $items;
                
                // Tambahkan ke koleksi jika total penjualan > 0
                if ($sellerTotal > 0) {
                    $orders->push($order);
                    $totalSales += $sellerTotal;
                    $totalItems += $items->sum('quantity');
                    $processedOrderIds[] = $order->id;
                }
            }
        });

        // Siapkan teks periode
        $periodText = 'Semua Periode';
        if ($startDate && $endDate) {
            $periodText = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }

        // Pilih export berdasarkan tipe
        switch ($type) {
            case 'excel':
                return Excel::download(
                    new SalesReportExport($orders, $startDate, $endDate), 
                    'sales_report.xlsx'
                );
            
            case 'pdf':
                $pdf = PDF::loadView('sales.sales_report_pdf', [
                    'orders' => $orders,
                    'totalSales' => $totalSales,
                    'totalItems' => $totalItems,
                    'periodText' => $periodText
                ]);

                return $pdf->download('sales_report.pdf');
            
            default:
                return back()->with('error', 'Tipe export tidak valid');
        }

    } catch (\Exception $e) {
        \Log::error('Export Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
    }
}


}