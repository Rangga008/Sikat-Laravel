<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;


class OrderController extends Controller
{   
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    // Konstanta untuk status dan metode pembayaran
    private const PAYMENT_METHODS = ['transfer', 'cod'];
    private const ORDER_STATUSES = ['pending', 'processing', 'completed', 'cancelled'];

    public function store(Request $request)
{
    $validated = $request->validate([
        'address' => 'required|string',
        'phone' => 'required|string',
        'payment_method' => 'required|in:transfer,cod',
        'notes' => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();

        // Ambil item cart user
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        // Validasi stok
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return redirect()->back()->with('error', 'Stok produk ' . $item->product->name . ' tidak mencukupi');
            }
        }

        // Buat order
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            }),
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_method'] == 'transfer' ? 'unpaid' : 'paid',
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'seller_confirmation_required' => $validated['payment_method'] == 'transfer'
        ]);

        // Buat order items
        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'subtotal' => $item->quantity * $item->product->price
            ];

            // Kurangi stok produk
            $item->product->decrement('stock', $item->quantity);
            $item->product->increment('sold_count', $item->quantity);
        }

        OrderItem::insert($orderItems);

        // Hapus cart
        Cart::where('user_id', auth()->id())->delete();

        DB::commit();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat');

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Order Creation Error', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id()
        ]);

        return redirect()->back()
            ->with('error', 'Gagal membuat pesanan');
    }
}

public function uploadPaymentProof(Request $request, Order $order)
{
    // Validasi kepemilikan order
    if ($order->user_id !== auth()->id()) {
        return redirect()->back()->with('error', 'Anda tidak memiliki akses');
    }
    // Gunakan path public
    $path = $request->file('payment_proof')->store('payment_proofs', 'public');
    

    // Validasi metode pembayaran dan status
    if ($order->payment_method !== 'transfer' || $order->payment_status !== 'unpaid') {
        return redirect()->back()->with('error', 'Pembayaran tidak dapat diproses');
    }

    $validated = $request->validate([
        'payment_proof' => 'required|image|max:2048', // Maks 2MB
        'bank_name' => 'required|string|max:100',
        'account_name' => 'required|string|max:100',
        'transfer_date' => 'required|date'
    ]);

    try {
        DB::beginTransaction();

        // Hapus bukti pembayaran lama jika ada
        if ($order->payment_proof) {
            Storage::delete($order->payment_proof);
        }

        // Upload bukti pembayaran baru
        $path = $request->file('payment_proof')->store('payment_proofs');

        // Update order dengan informasi pembayaran
        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'pending', // Berubah ke status pending
            'seller_confirmation_required' => true,
            'bank_name' => $validated['bank_name'],
            'account_name' => $validated['account_name'],
            'transfer_date' => $validated['transfer_date']
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi penjual.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Payment Proof Upload Error', [
            'order_id' => $order->id,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()->with('error', 'Gagal mengupload bukti pembayaran');
    }
}

    // Method pembayaran
    public function payment(Order $order)
    {
        $this->authorize('view', $order);
        return view('orders.payment-confirmation', compact('order'));
    }
    

    // Konfirmasi pembayaran
    public function confirmPayment(Request $request, Order $order)
{
    // Validasi akses
    if (!auth()->user()->hasRole('restaurant')) {
        return redirect()->back()->with('error', 'Anda tidak memiliki akses');
    }

    // Validasi kondisi pembayaran
    if ($order->payment_method !== 'transfer' || 
        $order->payment_status !== 'pending' || 
        !$order->seller_confirmation_required ||
        !$order->payment_proof) {
        
        return redirect()->back()->with('error', 'Pembayaran tidak dapat dikonfirmasi');
    }

    try {
        DB::beginTransaction();

        // Proses konfirmasi pembayaran
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'seller_confirmation_required' => false
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi');

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Payment Confirmation Error', [
            'order_id' => $order->id,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()->with('error', 'Gagal mengkonfirmasi pembayaran');
    }
}
// Method untuk melihat detail order di admin


public function restaurantOrderIndex(Request $request)
    {
        $query = Order::query();

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter berdasarkan metode pembayaran
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'), 
                $request->input('end_date')
            ]);
        }

        $orders = $query->with(['user', 'items'])
            ->latest()
            ->paginate(20);

        return view('restaurant.orders.index', compact('orders'));
    }

    public function restaurantOrderShow(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('restaurant.orders.show', compact('order'));
    }

    public function confirmCodPayment(Order $order)
    {
        // Pastikan hanya admin atau penjual yang bisa konfirmasi
        $this->authorize('update', $order);

        try {
            DB::beginTransaction();

            // Update status order
            $order->update([
                'status' => 'processing', // Atau 'completed'
                'payment_status' => 'paid'
            ]);

            // Kurangi stok produk
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->decrement('stock', $item->quantity);
                $product->increment('sold_count', $item->quantity);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran COD berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    // Metode untuk mengubah status order
    

    // Tambahan: Metode untuk melihat detail pembayaran COD
    public function showCodPaymentDetails($orderId)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($orderId);

        return view('orders.cod-payment-details', [
            'order' => $order
        ]);
    }

    public function generateInvoice(Order $order)
{
    // Debug path gambar
    $order->load([
        'user', 
        'items.product'
    ]);

    // Log path gambar untuk debugging
    $order->items->each(function($item) {
        \Log::info('Product Image Debug', [
            'product_id' => $item->product_id,
            'product_name' => $item->product->name,
            'image_path' => $item->product->image,
            'image_url' => $item->product->image_url,
            'full_image_path' => $item->product->image ? storage_path('app/public/' . $item->product->image) : 'No image'
        ]);
    });

    // Konversi path storage ke path absolut
    $order->items->each(function($item) {
        if ($item->product->image) {
            $item->product->absolute_image_path = storage_path('app/public/' . $item->product->image);
        }
    });

    // Generate PDF
    $pdf = PDF::loadView('orders.invoice', compact('order'));
    
    // Set nama file
    $filename = 'invoice_' . $order->id . '.pdf';

    return $pdf->download($filename);
}

    // Konfirmasi pengiriman
    public function confirmDelivery(Order $order)
    {
        $this->authorize('update', $order);

        $order->update([
            'status' => 'completed',
            'delivered_at' => now()
        ]);

        return redirect()->back()->with('success', 'Pengiriman dikonfirmasi');
    }

    // Daftar pesanan
    public function index()
    {
        $orders = Order::forUser()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Detail pesanan
    public function show(Order $order)
{
    // Pastikan order adalah milik user yang sedang login
    $this->authorize('view', $order);

    // Eager loading dengan relasi review untuk setiap item
    $order->load([
        'items' => function($query) {
            $query->with(['product', 'review' => function($reviewQuery) {
                $reviewQuery->where('user_id', auth()->id());
            }]);
        }
    ]);

    // Debug log
    \Log::info('Order Items Debug', [
        'order_id' => $order->id,
        'order_status' => $order->status,
        'items' => $order->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'has_review' => $item->has_review,
                'can_be_reviewed' => $item->can_be_reviewed
            ];
        })->toArray()
    ]);

    return view('orders.show', compact('order'));
}

    // Helper method untuk response error
    private function errorResponse($message)
    {
        return redirect()->back()->with('error', $message);
    }
    public function downloadInvoice(Order $order)
    {
        // Pastikan user hanya bisa download invoice miliknya
        $this->authorize('view', $order);

        // Load view invoice
        $pdf = PDF::loadView('orders.invoice', [
            'order' => $order->load('items.product', 'user')
        ]);

        // Generate nama file
        $filename = 'invoice_' . $order->id . '_' . now()->format('YmdHis') . '.pdf';

        // Download PDF
        return $pdf->download($filename);
    }
    public function paymentConfirmation(Order $order)
{
    // Pastikan hanya pemilik pesanan yang bisa mengakses
    $this->authorize('view', $order);

    // Pastikan pesanan dalam status pending dan metode pembayaran transfer
    if ($order->status !== 'pending' || $order->payment_method !== 'transfer') {
        return redirect()->route('orders.show', $order)
            ->with('error', 'Pesanan tidak dapat dikonfirmasi');
    }

    return view('orders.payment-confirmation', compact('order'));
}



public function tracking(Order $order)
{
    // Pastikan hanya pemilik pesanan yang bisa mengakses
    $this->authorize('view', $order);

    return view('orders.tracking', compact('order'));
}
public function sellerConfirmPayment(Request $request, Order $order)
{
    try {
        $order->update([
            'payment_status' => 'paid', // Gunakan nilai enum yang valid
            'status' => 'processing'
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi');
    } catch (\Exception $e) {
        Log::error('Payment Confirmation Error', [
            'order_id' => $order->id,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()->with('error', 'Gagal mengkonfirmasi pembayaran');
    }
}
    public function restaurantUpdateOrderStatus(Request $request, Order $order)
    {
        // Pastikan hanya restaurant yang bisa update status
        if (Auth::user()->role !== 'restaurant') {
            abort(403, 'Anda tidak memiliki izin mengakses halaman ini');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        try {
            $order->update($validated);

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Order Status Update Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Gagal memperbarui status pesanan');
        }
    }
    public function salesReport(Request $request)
{
    // Query untuk mendapatkan orders
    $query = Order::where('payment_status', 'paid')
        ->where('status', 'completed');

    // Filter berdasarkan periode
    if ($request->has('period')) {
        $query->when($request->input('period'), function ($q, $period) {
            return match($period) {
                'today' => $q->whereDate('created_at', today()),
                'this_week' => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'last_month' => $q->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year),
                'this_year' => $q->whereYear('created_at', now()->year),
                default => $q
            };
        });
    }

    // Filter berdasarkan tanggal custom
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            $request->input('start_date'), 
            $request->input('end_date')
        ]);
    }

    // Ambil orders
    $orders = $query->latest()->get();

    // Hitung total penjualan
    $totalSales = $orders->sum('total_amount');

    // Hitung total item terjual
    $totalItems = $orders->sum(function($order) {
        return $order->items->sum('quantity');
    });

    // Ekspor jika diminta
    if ($request->has('export')) {
        return match($request->input('export')) {
            'pdf' => $this->exportSalesReportPDF($orders, $totalSales, $totalItems),
            'excel' => $this->exportSalesReportExcel($orders, $totalSales, $totalItems),
            default => null
        };
    }

    // Return view
    return view('reports.sales', [
        'orders' => $orders,
        'totalSales' => $totalSales,
        'totalItems' => $totalItems
    ]);
}

// Metode ekspor PDF
private function exportSalesReportPDF($orders, $totalSales, $totalItems)
{
    $pdf = PDF::loadView('reports.exports.sales-pdf', [
        'orders' => $orders,
        'totalSales' => $totalSales,
        'totalItems' => $totalItems
    ]);

    return $pdf->download('sales_report_' . now()->format('YmdHis') . '.pdf');
}

public function viewSalesReport(User $user)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }

    public function manageSales(User $user)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }

    public function view(User $user, Order $order)
    {
        // Hanya pemilik order atau admin/restaurant yang bisa melihat
        return $user->id === $order->user_id || 
               $user->hasRole(['admin', 'restaurant']);
    }

    public function updateOrderStatus(User $user, Order $order)
    {
        return $user->hasRole(['admin', 'restaurant']);
    }

   // Metode ekspor Excel
private function exportSalesReportExcel($orders, $totalSales, $totalItems)
{
    return Excel::download(
        new SalesReportExport($orders, $totalSales, $totalItems), 
        'sales_report_' . now()->format('YmdHis') . '.xlsx'
    );
}
public function downloadPaymentProof(Order $order)
{
    // Pastikan hanya pemilik restoran yang bisa download
    if (!auth()->user()->hasRole('restaurant')) {
        abort(403);
    }

    // Pastikan bukti pembayaran ada
    if (!$order->payment_proof) {
        abort(404, 'Bukti pembayaran tidak ditemukan');
    }

    // Download file
    return Storage::download($order->payment_proof, 'Bukti Pembayaran Pesanan #' . $order->id . '.jpg');
}
    // Method update status order
    public function adminUpdateOrderStatus(Request $request, Order $order)
    {
        // Pastikan hanya restaurant yang bisa update status
        $this->authorize('manageSales');

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            $order->update($validated);

            // Logika tambahan berdasarkan status
            if ($validated['status'] === 'completed') {
                // Pastikan stok produk dikurangi
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->decrement('stock', $item->quantity);
                    $product->increment('sold_count', $item->quantity);
                }
            } elseif ($validated['status'] === 'cancelled') {
                // Kembalikan stok produk
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->increment('stock', $item->quantity);
                    $product->decrement('sold_count', $item->quantity);
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Status pesanan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Admin Order Status Update Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui status pesanan: ' . $e->getMessage());
        }
    }
}