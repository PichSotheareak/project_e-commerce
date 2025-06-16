<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'brands', 'product_details']);
        if ($request->has('with_deleted') && $request->with_deleted) {
            $query->withTrashed();
        }
        return response()->json([
            'data' => $query->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'inStock' => 'required|integer|min:0',
            'categories_id' => 'required|exists:categories,id',
            'brands_id' => 'required|exists:brands,id',
            'product_details_id' => 'required|exists:product_details,id',

        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('product', 'public');
        }

        $product = Product::create($validated);
        $product->load(['categories', 'brands', 'product_details']);

        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['categories', 'brands', 'product_details'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'inStock' => 'required|integer|min:0',
            'categories_id' => 'required|exists:categories,id',
            'brands_id' => 'required|exists:brands,id',
            'product_details_id' => 'required|exists:product_details,id',

        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('product', 'public');
        } elseif ($request->remove_image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        } else {
            $validated['image'] = $product->image; // Preserve existing image
        }

        $product->update($validated);
        $product->load(['categories', 'brands', 'product_details']);

        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json([
            'message' => 'Product soft deleted successfully'
        ]);
    }
    public function restore(string $id)
    {
        $product = Product::withTrashed()->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->restore();
        $product->load(['categories', 'brands', 'product_details']);

        return response()->json([
            'data' => $product,
            'message' => 'Product restored successfully'
        ]);
    }
    public function forceDelete(string $id)
    {
        $product = Product::withTrashed()->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();
        return response()->json([
            'message' => 'Product permanently deleted successfully'
        ]);
    }
}
