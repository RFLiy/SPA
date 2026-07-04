<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\OrderStatusNotification;

class CheckoutController extends Controller {

    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('info', 'Cart masih kosong');
        }

        return view('checkout.index', compact('cartItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|min:20',
            'shipping_type' => 'required',
        ], [
            'shipping_address.min' => 'Alamat terlalu singkat! Harap masukkan alamat lengkap (Nama Jalan, No. Rumah, RT/RW, dan Patokan). Kemudian Lengkapi Alamat Diprofile Untuk Mempermudah',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
        ]);

        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        abort_if($cartItems->isEmpty(), 403);

        $order = null;

        DB::transaction(function () use ($cartItems, $request, &$order) {

            $total = $cartItems->sum(
                fn($i) => $i->quantity * $i->product->base_price
            );

            if ($request->shipping_type === 'internal') {
                $total;
            }

            $order = Order::create([
                'user_id'        => Auth::id(),
                'order_code'     => 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'total'          => $total,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'status'         => 'waiting_payment',
                'shipping_option' => $request->shipping_type,
                'shipping_address' => $request->shipping_address,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'order_id'=> $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->base_price,
                ]);
            }

            Cart::where('user_id', Auth::id())->delete();
        });

        /** @var \App\Models\User $customer */
        $customer = Auth::user();

        if ($customer && $order) {
            $customer->notify(new OrderStatusNotification($order, 'waiting_payment'));
        }

        return redirect()->route('orders.index')->with('success', 'Order created. Please proceed to payment.');
    }
}
