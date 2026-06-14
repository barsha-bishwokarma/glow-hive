<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index($id)
    {
        $reviews = Review::where('product_id', $id)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => ReviewResource::collection($reviews)
        ]);
    }

    public function store(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $exists = Review::where('product_id', $product->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $review = Review::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'rating'      => $request->rating,
            'title'       => $request->title,
            'body'        => $request->body,
            'is_approved' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'data'    => new ReviewResource($review->load('user'))
        ]);
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.'
            ]);
        }

        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own review.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        $review->update($request->only('rating', 'title', 'body'));

        return response()->json([
            'success' => true,
            'message' => 'Review updated.',
            'data'    => new ReviewResource($review->load('user'))
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.'
            ]);
        }

        if ($review->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own review.'
            ]);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted.'
        ]);
    }
}
