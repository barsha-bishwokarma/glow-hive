<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data'    => BrandResource::collection($brands)
        ]);
    }

    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new BrandResource($brand)
        ]);
    }
}
