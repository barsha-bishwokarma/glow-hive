<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // Customer places an order
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_ids'       => 'required|array',
            'cart_ids.*'     => 'required|exists:carts,id',
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'payment_method' => 'required|in:cash_on_delivery,khalti,esewa',
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

        $totalPrice = 0;
        $orderItems = [];

        foreach ($cart as $item) {
            $price      = $item->product->sale_price ?? $item->product->price;
            $totalPrice += $price * $item->quantity;

            $orderItems[] = [
                'product_id' => $item->product->id,
                'quantity'   => $item->quantity,
                'price'      => $price,
            ];
        }

        $order = Order::create([
            'user_id'        => $request->user()->id,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
            'address'        => $request->address,
            'phone'          => $request->phone,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
        ]);

        $order->items()->createMany($orderItems);

        foreach ($cart as $item) {
            $item->product->decrement('stock_quantity', $item->quantity);
        }

        Cart::whereIn('id', $request->cart_ids)->delete();

        // Check payment method
        if ($request->payment_method == 'cash_on_delivery') {
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully. Pay on delivery.',
                'data'    => new OrderResource($order->load('items.product'))
            ]);
        }

        if ($request->payment_method == 'khalti') {

            $khaltiResponse = Http::withHeaders([
                "Authorization" => "Key " . env("KHALTI_SECRET"),
            ])->post(env('KHALTI_BASE_URL') . '/epayment/initiate/', [
                'return_url'    => env('APP_URL') . '/api/khalti/callback',
                'website_url'   => env('APP_URL'),
                'amount'        => $totalPrice * 100, // in paisa
                'purchase_order_id'   => $order->id,
                'purchase_order_name' => 'Glow Hive Order #' . $order->id,
            ]);


            if ($khaltiResponse->successful()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Proceed to Khalti payment.',
                    'data'        => new OrderResource($order->load('items.product')),
                    'payment_url' => $khaltiResponse['payment_url']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Khalti payment initiation failed.'
            ]);
        }
    }

    // Customer views their orders
    public function myOrders(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders)
        ]);
    }

    // Customer views single order
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => new OrderResource($order)
        ]);
    }

    // Customer cancels order
    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ]);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled.'
            ]);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }
}
