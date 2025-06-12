<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Customer::all(),
            'count' => Customer::count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|max:6',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:15|unique:customers,phone',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageCustomer = null;
        if ($request->hasFile('image')) {
            $imageCustomer = $request->file('image')->store('customer', 'public');
        }

        $customer = Customer::create([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'address' => $request -> address,
            'password' => Hash::make($request -> password),
            'image' => $imageCustomer,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $customer,
            'message' => 'Customer created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => Customer::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id);
        $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|max:6',
            'email' => 'required|email|unique:customers,email,' .$id,
            'phone' => 'nullable|string|max:15|unique:customers,phone,' .$id,
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }

            $imageCustomer = $request->file('image')->store('customer', 'public');
            $customer->image = $imageCustomer;
        }

        $customer -> update([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'address' => $request -> address,
            'password' => Hash::make($request -> password),
            'image' => $imageCustomer,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $customer,
            'message' => 'Customer updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        $customer -> delete();
        return response()->json([
            'message' => 'Customer deleted successfully'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'customer' => JWTAuth::customer()->load('profile')
        ]);
    }
}
