<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDetails;
use Illuminate\Http\Request;

class ProductDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => ProductDetails::all(),
            'count' => ProductDetails::count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'model' =>  'required|string|max:255',
            'processor' =>  'required|string|max:255',
            'ram' =>  'required|string|max:255',
            'storage' =>  'required|string|max:255',
            'display' =>  'required|string|max:255',
            'graphics' =>  'required|string|max:255',
            'os' =>  'required|string|max:255',
            'battery' =>  'required|string|max:255',
            'weight' =>  'required|string|max:255',
            'warranty' =>  'required|string|max:255',
        ]);

        $productDetail = ProductDetails::create([
            'model' => $request -> model,
            'processor' => $request -> processor,
            'ram' => $request -> ram,
            'storage' => $request -> storage,
            'display' => $request -> display,
            'graphics' => $request -> graphics,
            'os' => $request -> os,
            'battery' => $request -> battery,
            'weight' => $request -> weight,
            'warranty' => $request -> warranty,
            'create_at' => now(),

        ]);
        return response()->json([
            'data' => $productDetail,
            'message' => 'Product details added successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => ProductDetails::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $productDetail = ProductDetails::find($id);
        $request->validate([
            'model' =>  'required|string|max:255',
            'processor' =>  'required|string|max:255',
            'ram' =>  'required|string|max:255',
            'storage' =>  'required|string|max:255',
            'display' =>  'required|string|max:255',
            'graphics' =>  'required|string|max:255',
            'os' =>  'required|string|max:255',
            'battery' =>  'required|string|max:255',
            'weight' =>  'required|string|max:255',
            'warranty' =>  'required|string|max:255',
        ]);

        $productDetail->update([
            'model' => $request -> model,
            'processor' => $request -> processor,
            'ram' => $request -> ram,
            'storage' => $request -> storage,
            'display' => $request -> display,
            'graphics' => $request -> graphics,
            'os' => $request -> os,
            'battery' => $request -> battery,
            'weight' => $request -> weight,
            'warranty' => $request -> warranty,
            'update_at' => now(),
        ]);
        return response()->json([
            'data' => $productDetail,
            'message' => 'Product details updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productDetail = ProductDetails::find($id);
        $productDetail->delete();
        return response()->json([
            'message' => 'Product Details deleted successfully'
        ]);
    }
}
