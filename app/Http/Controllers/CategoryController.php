<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->has('with_deleted') && $request->with_deleted) {
            $query->withTrashed();
        }
        return response()->json(['data' => $query->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
        ]);

        $category = Category::create($validated);
        return response()->json([
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['data' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
        ]);

        $category->update($validated);
        return response()->json([
            'data' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
    public function restore(string $id)
    {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->restore();
        return response()->json([
            'data' => $category,
            'message' => 'Category restored successfully'
        ]);
    }

    public function forceDelete(string $id)
    {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->forceDelete();
        return response()->json(['message' => 'Category permanently deleted successfully']);
    }
}
