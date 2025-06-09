<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => PaymentMethod::all(),
            'count' => PaymentMethod::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'qrcode' => 'required',

        ]);

        $qrcode = null;
        if ($request->hasFile('qrcode')) {
            $qrcode = $request->file('qrcode')->store('qrcode', 'public');
        }

        $paymentMethod = PaymentMethod::create([
            'name' => $request-> name,
            'account_number' => $request -> account_number,
            'qrcode' => $qrcode,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $paymentMethod,
            'message' => 'Payment Method has been created'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => PaymentMethod::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'qrcode' => 'required',

        ]);

        if ($request->hasFile('qrcode')) {
            if ($paymentMethod->qrcode) {
                Storage::disk('public')->delete($paymentMethod->qrcode);
            }

            $qrcode = $request->file('qrcode')->store('qrcode', 'public');
            $paymentMethod->qrcode = $qrcode;
        }

        $paymentMethod -> update([
            'name' => $request-> name,
            'account_number' => $request -> account_number,
            'qrcode' => $qrcode,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $paymentMethod,
            'message' => 'Payment Method has been updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        $paymentMethod -> delete();
        return response()->json([
            'message' => 'Payment Method has been deleted'
        ]);
    }
}
