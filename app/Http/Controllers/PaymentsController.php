<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Payments::with(['invoices', 'branches', 'paymentMethod'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
           'invoice_id' => 'required|exists:invoices,id',
           'payment_date' => 'required|date|date_format:Y-m-d',
           'amount' => 'required|numeric|min:0',
           'payment_method_id' => 'required|exists:payment_methods,id',
           'branch_id' => 'required|exists:branches,id',
        ]);

        $payments = Payments::create([
           'invoice_id' => $request -> invoice_id,
           'payment_date' => $request -> payment_date,
           'amount' => $request -> amount,
           'payment_method_id' => $request -> payment_method_id,
           'branch_id' => $request -> branch_id,
            'created_at' => now(),

        ]);
        return response()->json([
            'data' => $payments,
            'message' => 'Payment added successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payments = Payments::find($id);

        if (! $payments) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payments->load(['invoices', 'branches', 'paymentMethod']);
        return response()->json([$payments]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payments = Payments::find($id);
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date|date_format:Y-m-d',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $payments -> update([
            'invoice_id' => $request -> invoice_id,
            'payment_date' => $request -> payment_date,
            'amount' => $request -> amount,
            'payment_method_id' => $request -> payment_method_id,
            'branch_id' => $request -> branch_id,
        ]);
        return response()->json([
            'data' => $payments,
            'message' => 'Payment updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payments = Payments::find($id);

        if (! $payments) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payments -> delete();
        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
