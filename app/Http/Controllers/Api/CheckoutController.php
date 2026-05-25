<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_ids'   => 'required|array',
            'cart_ids.*' => 'required|exists:carts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $cart = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $request->cart_ids)
            ->get();

        if ($cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected.'
            ]);
        }

        $items      = [];
        $totalPrice = 0;

        foreach ($cart as $item) {
            if (!$item->product->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => $item->product->name . ' is no longer available.'
                ]);
            }

            if ($item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => $item->product->name . ' has only ' .
                        $item->product->stock_quantity . ' items left.'
                ]);
            }

            $price      = $item->product->sale_price ?? $item->product->price;
            $subtotal   = $price * $item->quantity;
            $totalPrice += $subtotal;

            $items[] = [
                'cart_id'      => $item->id,
                'product_id'   => $item->product->id,
                'product_name' => $item->product->name,
                'image'        => $item->product->image,
                'price'        => $price,
                'quantity'     => $item->quantity,
                'subtotal'     => $subtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'items'           => $items,
                'total'           => $totalPrice,
                'payment_methods' => [
                    'cash_on_delivery',
                    'khalti',
                    'esewa'
                ]
            ]
        ]);
    }
}
