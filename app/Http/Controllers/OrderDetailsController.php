<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use Illuminate\Http\Request;

class OrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => OrderDetails::with(['product', 'orders'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'orders_id' => 'required|exists:orders,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $orderDetails = OrderDetails::create([
            'product_id' => $request -> product_id,
            'orders_id' => $request -> orders_id,
            'quantity' => $request -> quantity,
            'price' => $request -> price,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $orderDetails,
            'message' => 'Order added successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderDetails = OrderDetails::find($id);

        if (!$orderDetails) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderDetails->load(['product', 'orders']);
        return response()->json([$orderDetails]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderDetails = OrderDetails::find($id);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'orders_id' => 'required|exists:orders,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $orderDetails -> update([
            'product_id' => $request -> product_id,
            'orders_id' => $request -> orders_id,
            'quantity' => $request -> quantity,
            'price' => $request -> price,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $orderDetails,
            'message' => 'Order updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderDetails = OrderDetails::find($id);

        if (!$orderDetails) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderDetails -> delete();
        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}
