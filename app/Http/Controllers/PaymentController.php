<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rfq;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function payProduct(Product $product, Request $request)
    {
        $qty = $request->input('quantity', 1);

        $grossAmount = $product->price * $qty;

        $snapToken = MidtransService::createSnapToken([
            'transaction_details' => [
                'order_id' => 'PROD-' . $product->id . '-' . time(),
                'gross_amount' => (int) $grossAmount,
            ],
            'item_details' => [
                [
                    'id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'name' => $product->name,
                ]
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ]);

        return redirect()->route('products.show', $product->id)->with('snapToken', $snapToken);
    }
}
