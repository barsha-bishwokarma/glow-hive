<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ← add this for image deletion
use Illuminate\Support\Str;             // ← add this for slug

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->where('is_active', true);

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by brand
        if ($brandId = $request->query('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        // Filter by price range
        if ($minPrice = $request->query('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->query('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Filter featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $products = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products)
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'brand'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]); // ← add 404
        }

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => ['required', 'exists:categories,id'],
            'brand_id'       => ['required', 'exists:brands,id'],
            'name'           => ['required', 'string', 'max:255', 'unique:products,name'], // ← add unique
            'description'    => ['nullable', 'string'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'price'          => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_featured'    => ['boolean'],
        ]);

        // ← auto generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true, // ← was missing
            'message' => 'Product created.',
            'data'    => new ProductResource($product)
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $validated = $request->validate([
            'category_id'    => ['sometimes', 'exists:categories,id'],
            'brand_id'       => ['sometimes', 'exists:brands,id'],
            'name'           => ['sometimes', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'price'          => ['sometimes', 'numeric', 'min:0'],
            'sale_price'     => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'is_featured'    => ['boolean'],
            'is_active'      => ['boolean'],
        ]);

        // ← if name changes, regenerate slug
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // ← if new image uploaded, delete old image first
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image); // delete old
            }
            $validated['image'] = $request->file('image')->store('products', 'public'); // save new
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated.',
            'data'    => new ProductResource($product->fresh(['category', 'brand']))
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]); // ← add 404
        }

        // ← delete image from storage before deleting product
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted.'
        ]);
    }
}
