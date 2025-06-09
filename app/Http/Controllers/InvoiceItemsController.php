<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItems;
use Illuminate\Http\Request;

class InvoiceItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => InvoiceItems::with(['invoice', 'product'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|',
            'sub_total' => 'required|numeric|',

        ]);

        $invoiceItems = InvoiceItems::create([
            'invoice_id' => $request -> invoice_id,
            'product_id' => $request -> product_id,
            'quantity' => $request -> quantity,
            'price' => $request -> price,
            'sub_total' => $request -> sub_total,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $invoiceItems,
            'message' => 'Invoice item created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoiceItems = InvoiceItems::find($id);

        if (!$invoiceItems) {
            return response()->json(['message' => 'Invoice item not found'], 404);
        }

        $invoiceItems -> load(['invoice', 'product']);
        return response()->json([$invoiceItems]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoiceItems = InvoiceItems::find($id);
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|',
            'sub_total' => 'required|numeric|',

        ]);

        $invoiceItems -> update([
            'invoice_id' => $request -> invoice_id,
            'product_id' => $request -> product_id,
            'quantity' => $request -> quantity,
            'price' => $request -> price,
            'sub_total' => $request -> sub_total,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $invoiceItems,
            'message' => 'Invoice item updated successfully'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoiceItems = InvoiceItems::find($id);

        if (!$invoiceItems) {
            return response()->json(['message' => 'Invoice item not found'], 404);
        }
        $invoiceItems -> delete();
        return response()->json([
            'message' => 'Invoice item deleted successfully'
        ]);
    }
}
