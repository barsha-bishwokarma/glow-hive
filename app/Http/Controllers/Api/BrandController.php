<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brand = Brand::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data'    => BrandResource::collection($brand)
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
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => new BrandResource($brand)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:brands,name'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $brand = Brand::create($validated);

        return response()->json([
            'message' => 'Brand created.',
            'data'    =>  new BrandResource($brand)
        ]);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.'
            ]);
        }

        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]);

        // if name is updated, regenerate slug too
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $brand->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated.',
            'data'    => new BrandResource($brand)
        ]);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'brand not found.'
            ]);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted.'
        ]);
    }
}
