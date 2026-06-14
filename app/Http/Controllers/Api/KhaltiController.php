<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KhaltiController extends Controller
{
    public function callback(Request $request)
    {
        $verifyResponse = Http::withHeaders([
            'Authorization' => 'Key ' . env('KHALTI_SECRET'),
        ])->post(env('KHALTI_BASE_URL') . '/epayment/lookup/', [
            'pidx' => $request->pidx,
        ]);

        $paymentData = $verifyResponse->json();

        $order = Order::find($paymentData['purchase_order_id']);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ]);
        }

        if ($paymentData['status'] === 'Completed') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful. Order is being processed.'
            ]);
        }

        $order->update(['payment_status' => 'failed']);

        return response()->json([
            'success' => false,
            'message' => 'Payment failed.'
        ]);
    }
}
