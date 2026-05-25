<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // View cart
    public function index(Request $request)
    {
        $cart = Cart::with(['product' => function ($query) {
            $query->select('id', 'name', 'image', 'price', 'sale_price');
        }])
            ->where('user_id', $request->user()->id)
            ->get();

        $total = $cart->sum(function ($item) {
            $price = $item->product->sale_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'data'    => CartResource::collection($cart),
            'total'   => $total
        ]);
    }

    // Add to cart
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + ($request->quantity ?? 1)
            ]);
        } else {
            $cart = Cart::create([
                'user_id'    => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data'    => new CartResource($cart->load('product'))
        ]);
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->find($id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ]);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'data'    => new CartResource($cart->load('product'))
        ]);
    }

    // Remove from cart
    public function destroy(Request $request, $id)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->find($id);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ]);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart.'
        ]);
    }

    // Clear cart
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.'
        ]);
    }
}
