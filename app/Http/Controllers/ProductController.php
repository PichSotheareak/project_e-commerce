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
    public function index()
    {
        return response()->json([
            'data' => Product::with(['categories', 'brands', 'product_details'])->get()
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

        $imageProduct = null;
        if ($request->hasFile('image')) {
            $imageProduct = $request->file('image')->store('product', 'public');
        }

        $product = Product::create([
            'name' => $request -> name,
            'description' => $request -> description,
            'image' => $imageProduct,
            'cost' => $request -> cost,
            'price' => $request -> price,
            'inStock' => $request -> inStock,
            'categories_id' => $request -> categories_id,
            'brands_id' => $request -> brands_id,
            'product_details_id' => $request -> product_details_id,
            'create_at' => now(),

        ]);
        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if(!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->load(['categories', 'brands', 'product_details']);
        return response()->json([$product]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
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
            $imageProduct = $request->file('image')->store('product', 'public');
            $product->image = $imageProduct;
        }

        $product -> update([
            'name' => $request -> name,
            'description' => $request -> description,
            'image' => $imageProduct,
            'cost' => $request -> cost,
            'price' => $request -> price,
            'inStock' => $request -> inStock,
            'categories_id' => $request -> categories_id,
            'brands_id' => $request -> brands_id,
            'product_details_id' => $request -> product_details_id,
            'update_at' => now(),
        ]);
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

        $product -> delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
