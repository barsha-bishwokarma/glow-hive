<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    // Upload images for a product
    public function store(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'images'   => 'required|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $hasPrimary = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->exists();

        $uploadedImages = [];

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');

            $uploadedImages[] = ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => !$hasPrimary && $index === 0,
                'sort_order' => $index,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully.',
            'data'    => ProductImageResource::collection(collect($uploadedImages))
        ]);
    }

    // View all images of a product
    public function index($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $images = ProductImage::where('product_id', $id)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductImageResource::collection($images)
        ]);
    }

    // Set image as primary
    public function setPrimary(Request $request, $id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ]);
        }

        // Remove primary from all images of this product
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated.',
            'data'    => new ProductImageResource($image)
        ]);
    }

    // Delete image
    public function destroy($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ]);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted.'
        ]);
    }
}
