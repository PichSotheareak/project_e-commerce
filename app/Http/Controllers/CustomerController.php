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
    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->has('with_deleted') && $request->with_deleted) {
            $query->withTrashed();
        }
        return response()->json(['data' => $query->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|max:6',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:15|unique:customers,phone',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('customers', 'public');
        }

        $customer = Customer::create($validated);
        return response()->json([
            'data' => $customer,
            'message' => 'Customer created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(['data' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|max:6',
            'email' => 'required|email|unique:customers,email,' .$id,
            'phone' => 'nullable|string|max:15|unique:customers,phone,' .$id,
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }
            $validated['image'] = $request->file('image')->store('customers', 'public');
        } elseif ($request->input('remove_image') == '1') {
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }
            $validated['image'] = null;
        } else {
            $validated['image'] = $customer->image;
        }

        $customer->update($validated);
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
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer soft deleted successfully']);
    }

    public function restore(string $id)
    {
        $customer = Customer::withTrashed()->find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->restore();
        return response()->json([
            'data' => $customer,
            'message' => 'Customer restored successfully'
        ]);
    }

    public function forceDelete(string $id)
    {
        $customer = Customer::withTrashed()->find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        if ($customer->image) {
            Storage::disk('public')->delete($customer->image);
        }

        $customer->forceDelete();
        return response()->json(['message' => 'Customer permanently deleted successfully']);
    }

    public function login(Request $request)
    {
        // ✅ Step 1 — Validate request input
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ Step 2 — Retrieve customer by email
        $customer = Customer::where('email', $validated['email'])->first();

        // ✅ Step 3 — Handle invalid credentials
        if (!$customer || !Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // ✅ Step 4 — Prepare clean customer data to return (hide sensitive data)
        $customerData = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at
        ];

        // ✅ Step 5 — Return success response
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'customer' => $customerData
        ], 200);
    }
}
