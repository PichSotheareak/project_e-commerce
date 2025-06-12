<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
           'data' => Orders::with(['customers', 'users', 'branches'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'users_id' => 'required|exists:users,id',
            'branches_id' => 'required|exists:branches,id',
            'order_date' => 'required|date|date_format:Y-m-d',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:pending,processing,completed,cancelled',
            'remarks' => 'nullable|string|min:50',
        ]);

        $orders = Orders::create([
            'customers_id' => $request-> customers_id,
            'users_id' => $request-> users_id,
            'branches_id' => $request-> branches_id,
            'order_date' => $request-> order_date,
            'total_amount' => $request-> total_amount,
            'status' => $request-> status,
            'payment_status' => $request-> payment_status,
            'remarks' => $request -> remarks,
            'created_at' => now(),

        ]);
        return response()->json([
            'data' => $orders,
            'message' => 'Order created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orders = Orders::find($id);

        if(!$orders){
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orders->load(['customers', 'users', 'branches']);
        return response()->json([$orders]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orders = Orders::find($id);
        $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'users_id' => 'required|exists:users,id',
            'branches_id' => 'required|exists:branches,id',
            'order_date' => 'required|date|date_format:Y-m-d',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:pending,processing,completed,cancelled',
            'remarks' => 'nullable|string|min:50',
        ]);

        $orders->update([
            'customers_id' => $request-> customers_id,
            'users_id' => $request-> users_id,
            'branches_id' => $request-> branches_id,
            'order_date' => $request-> order_date,
            'total_amount' => $request-> total_amount,
            'status' => $request-> status,
            'payment_status' => $request-> payment_status,
            'remarks' => $request -> remarks,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $orders,
            'message' => 'Order updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orders = Orders::find($id);
        $orders->delete();
        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}
