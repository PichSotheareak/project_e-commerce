<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Brand::all(),
            'count' => Brand::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('images', 'public');
        }

        $brand = Brand::create([
            'name' => $request -> name,
            'image' => $image,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $brand,
            'message' => 'Brand created successfully'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => Brand::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);
        $request->validate([
            'name' => 'required|string|max:50',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }

            $image = $request->file('image')->store('images', 'public');
            $brand->image = $image;
        }

        $brand -> update([
            'name' => $request -> name,
            'image' => $image,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $brand,
            'message' => 'Brand updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        return response()->json([
            'message' => 'Record deleted successfully.'
        ]);
    }
    public function restore(string $id)
    {
        $brand = Brand::withTrashed()->find($id);
        if (!$brand) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $brand->restore();
        return response()->json([
            'data' => $brand,
            'message' => 'Branch restored successfully'
        ]);
    }

    public function forceDelete(string $id)
    {
        $brand = Brand::withTrashed()->find($id);
        if (!$brand) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->forceDelete();
        return response()->json(['message' => 'Branch permanently deleted successfully']);
    }
}
