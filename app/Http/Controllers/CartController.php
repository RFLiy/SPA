<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with('product')
            ->get();

        return view('cart.index', compact('cartItems'));
    }

    public function add(Product $product)
    {
        if ($product->stock <= 0) {
            return back()->with('error', 'Maaf, stok barang ini sudah habis.');
        }

        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();

        if ($cart) {
            if (($cart->quantity + 1) > $product->stock) {
                return back()->with('error', 'Maaf, stok tidak mencukupi untuk menambah jumlah.');
            }
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function increment(Cart $cart)
    {
        if ($cart->quantity + 1 > $cart->product->stock) {
            return back()->with('error', 'Stok terbatas, tidak bisa menambah jumlah lagi.');
        }

        $cart->increment('quantity');
        return back();
    }


    public function decrement(Cart $cart)
    {
        if ($cart->quantity > 1) {
            $cart->decrement('quantity');
        } else {
            $cart->delete();
        }
        return back();
    }

    public function remove(Cart $cart)
    {
        $cart->delete();
        return back();
    }

    public function store(Product $product)
    {
        if ($product->is_customizable) {
            return redirect()
                ->back()
                ->with('error', 'Produk ini harus melalui RFQ.');
        }

        Cart::updateOrCreate(
            [
                'user_id' => Auth::user(),
                'product_id' => $product->id
            ],
            [
                'quantity' => DB::raw('quantity + 1')
            ]
        );

        return redirect()->route('cart.index');
    }
}
