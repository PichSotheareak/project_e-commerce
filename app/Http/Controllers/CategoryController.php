<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Category::all(),
            'total' => Category::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
        ]);

        $category = Category::create([
            'name' => $request -> name,
            'description' => $request -> description,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $category,
            'message' => 'Category created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => Category::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
        ]);

        $category->update([
            'name' => $request -> name,
            'description' => $request -> description,
            'updated_at' => now(),
        ]);
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
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
