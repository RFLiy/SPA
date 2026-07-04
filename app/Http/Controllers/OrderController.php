<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Cart;
use App\Models\Rfq;
use App\Notifications\OrderStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->base_price);
        $total = $subtotal;
        $transaction_details = [
            'order_id' => uniqid(),
            'gross_amount' => $total
        ];

        $customer_details = [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ];

        $midtrans_params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
        ];

        $snapToken = Snap::getSnapToken($midtrans_params);

        return view('order', compact('cartItems', 'subtotal', 'shipping', 'total', 'snapToken'));
    }

    public function store(Request $request)
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong.');
        }


        return DB::transaction(function () use ($cartItems, $request) {

            $total = $cartItems->sum(fn($item) => $item->quantity * $item->product->base_price) + 10000;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'waiting_payment',
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'total' => $total,
                'shipping_option'  => $request->shipping_type,
                'shipping_address' => $request->shipping_address,
            ]);


            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->image,
                    'price' => $item->product->base_price,
                ]);

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => 'waiting_payment',
                    'description' => "Order dibuat untuk produk {$item->product->name}"
                ]);
            }
            Cart::where('user_id', Auth::id())->delete();

            return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat!');
        });
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product' => function ($query) {
            $query->withoutGlobalScopes();
        }]);
        $snapToken = null;

        if ($order->payment_status === 'pending' && $order->status === 'waiting_payment') {

            $midtransOrderId = $order->order_code . '-' . time();

            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            try {
                $snapToken = \Midtrans\Snap::getSnapToken([
                    'transaction_details' => [
                        'order_id' => $midtransOrderId,
                        'gross_amount' => (int) $order->total,
                    ],
                    'callbacks' => [
                        'finish' => route('orders.show', $order->id),
                        'unfinish' => route('orders.show', $order->id),
                        'error' => route('orders.show', $order->id),
                    ],
                    'expiry' => [
                        'start_time' => date("Y-m-d H:i:s O"),
                        'unit' => 'minute',
                        'duration' => 1
                    ],
                    'customer_details' => [
                        'first_name' => $order->user->name,
                        'email' => $order->user->email,
                    ],
                ]);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                Log::error("Midtrans Error: " . $e->getMessage());
            }
        }

        return view('orders.show', compact('order', 'snapToken'));
    }

    public function status(Order $order)
    {
        $this->authorize('view', $order);
        $statuses = $order->statuses()->latest()->get();
        return view('orders.status', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request)
    {
        $notifyOrder = null;
        $order = Order::with('items')->find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);
        }
        if ($order->payment_status !== 'paid') {

            DB::transaction(function () use ($order, &$notifyOrder) {
                $order->update([
                    'status'         => 'paid',
                    'payment_status' => 'paid',
                    $notifyOrder = $order->fresh(['user'])
                ]);

                if ($notifyOrder) {
                    $notifyOrder->user->notify(new OrderStatusNotification($notifyOrder, 'paid'));
                }

                foreach ($order->items as $item) {
                    \App\Models\Product::withoutGlobalScopes()
                        ->where('id', $item->product_id)
                        ->decrement('stock', $item->quantity);
                }

                OrderStatus::create([
                    'order_id'    => $order->id,
                    'status'      => 'paid',
                    'description' => "Pembayaran berhasil. Stok database & master produk telah diperbarui."
                ]);
            });
            $order->user->notify(new OrderStatusNotification($order, 'paid'));
        }

        return response()->json(['success' => true]);
    }

    public function midtransWebhook(Request $request)
    {
        $notification = json_decode($request->getContent());
        $midtransOrderId = $notification->order_id;
        $orderCode = \Illuminate\Support\Str::beforeLast($midtransOrderId, '-');
        $order = Order::where('order_code', $orderCode)
            ->with(['items.product', 'user'])
            ->first();

        if (!$order) {
            Log::error("Webhook Error: Order Code {$orderCode} tidak ditemukan.");
            return response()->json(['status' => 'order not found'], 404);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;

        return DB::transaction(function () use ($order, $transaction, $type, $fraud) {

            if ($transaction == 'capture' || $transaction == 'settlement') {
                if ($fraud == 'challenge') {
                    $order->update(['payment_status' => 'challenge']);
                } else {
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'status' => 'paid',
                            'payment_status' => 'paid'
                        ]);
                        foreach ($order->items as $item) {
                            \App\Models\Product::withoutGlobalScopes()
                                ->where('id', $item->product_id)
                                ->decrement('stock', $item->quantity);
                        }

                        OrderStatus::create([
                            'order_id'    => $order->id,
                            'status'      => 'paid',
                            'description' => "Pembayaran dikonfirmasi via {$type}. Stok telah dikurangi."
                        ]);
                        $order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'paid'));
                    }
                }
            } elseif ($transaction == 'pending') {
                $order->update([
                    'status' => 'waiting_payment',
                    'payment_status' => 'pending'
                ]);
            } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed'
                ]);

                OrderStatus::create([
                    'order_id'    => $order->id,
                    'status'      => 'cancelled',
                    'description' => "Transaksi gagal atau dibatalkan oleh sistem ({$transaction})."
                ]);

                $order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'cancelled'));
            }

            return response()->json(['status' => 'ok']);
        });
    }

    public function tracking($id)
    {
        $order = Order::with([
            'user',
            'statuses',
            'items.product'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.tracking', compact('order'));
    }

    public function downloadSuratJalan($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat-jalan', [
            'order' => $order
        ]);

        return $pdf->stream("SJ-Order-{$order->order_code}.pdf");
    }

    public function finish($id)
    {
        $order = Order::findOrFail($id);
        if ($order->user_id != Auth::id()) {
            abort(403);
        }

        $order->update([
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'completed',
            'description' => 'Pesanan telah diterima oleh pelanggan dan dinyatakan selesai.'
        ]);

        $order->user->notify(new OrderStatusNotification($order, 'completed'));

        return redirect()->route('orders.index')->with('success', 'Terima kasih! Pesanan dinyatakan selesai.');
    }

    public function cancel(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }

        if (in_array($order->status, ['waiting_payment', 'paid', 'processing'])) {

            DB::transaction(function () use ($order) {
                if (in_array($order->status, ['paid', 'processing'])) {
                    foreach ($order->items as $item) {
                        \App\Models\Product::withoutGlobalScopes()
                            ->where('id', $item->product_id)
                            ->increment('stock', $item->quantity);
                    }
                }

                $order->update(['status' => 'cancelled']);

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => 'cancelled',
                    'description' => 'Pesanan dibatalkan oleh pelanggan. Stok telah dikembalikan (jika ada).'
                ]);
            });

            $order->user->notify(new OrderStatusNotification($order, 'cancelled'));

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error', 'Pesanan tidak bisa dibatalkan.');
    }

    public function destroy(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (in_array($order->status, ['cancelled', 'waiting_payment'])) {
            $order->items()->delete();
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Riwayat pesanan berhasil dihapus.');
        }

        return back()->with('error', 'Pesanan aktif tidak dapat dihapus dari riwayat.');
    }


}
