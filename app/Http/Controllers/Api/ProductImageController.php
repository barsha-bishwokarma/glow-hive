<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    public function index($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $images = ProductImage::where('product_id', $id)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductImageResource::collection($images)
        ]);
    }
}
