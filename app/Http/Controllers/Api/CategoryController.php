<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // ← add this

class CategoryController extends Controller
{
    public function index()
    {
        // ->get() never returns null so remove the if(!$categories) check
        $categories = Category::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data'    => CategoryResource::collection($categories)
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        // ← auto generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created.',
            'data'    => new CategoryResource($category)
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]);

        // ← if name changes, update slug too
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated.',
            'data'    => new CategoryResource($category)
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted.'
        ]);
    }
}
