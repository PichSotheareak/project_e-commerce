<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Invoices::with(['customers', 'users', 'orders', 'paymentMethods'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'transaction_date' => 'required|date|date_format:Y-m-d',
            'pick_up_date_time' => 'required|dateTime|date_format:Y-m-d H:i:s',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,sent,draft',
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $invoices = Invoices::create([
            'user_id' => $request -> user_id,
            'customer_id' => $request -> customer_id,
            'transaction_date' => $request -> transaction_date,
            'pick_up_date_time' => $request -> pick_up_date_time,
            'total_amount' => $request -> total_amount,
            'paid_amount' => $request -> paid_amount,
            'status' => $request -> status,
            'order_id' => $request -> order_id,
            'payment_method_id' => $request -> payment_method_id,
            'created_at' => now(),

        ]);
        return response()->json([
            'data' => $invoices,
            'message' => 'Invoice Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoices = Invoices::find($id);

        if(!$invoices){
            return response()->json(['message' => 'Invoice Not Found'], 404);
        }

        $invoices->load(['customers', 'users', 'orders', 'paymentMethods']);
        return response()->json([$invoices]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoices = Invoices::find($id);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'transaction_date' => 'required|date|date_format:Y-m-d',
            'pick_up_date_time' => 'required|dateTime|date_format:Y-m-d H:i:s',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,sent,draft',
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $invoices->update([
            'user_id' => $request -> user_id,
            'customer_id' => $request -> customer_id,
            'transaction_date' => $request -> transaction_date,
            'pick_up_date_time' => $request -> pick_up_date_time,
            'total_amount' => $request -> total_amount,
            'paid_amount' => $request -> paid_amount,
            'status' => $request -> status,
            'order_id' => $request -> order_id,
            'payment_method_id' => $request -> payment_method_id,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $invoices,
            'message' => 'Invoice Updated Successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoices = Invoices::find($id);

        if (!$invoices) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $invoices->delete();
        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }
}
