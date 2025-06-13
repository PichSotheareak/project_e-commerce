<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        return response()->json([
            "data" => User::with('profile')->get()
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|in:male,female,other',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'nullable|string|in:enable,disable',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'gender' => $request->gender,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request -> status,
                'created_at' => now(),
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('profile', 'public');
            }
            else{
                $image = null;
            }

            Profile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'type' => $request->type,
                'image' => $image,
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'data' => $user->load('profile'),
                'message' => 'User created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'something went wrong: ' . $e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->load('profile');
        return response()->json([$user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'nullable|string|in:male,female,other',
            'email' => 'required|email|unique:users,email,' .$id,
            'password' => 'required|string|min:8',
            'status' => 'nullable|string|in:enable,disable',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $user -> update([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'password' => Hash::make($request -> password),
            'status' => $request -> status,
            'updated_at' => now(),
        ]);

        $profile = Profile::where("user_id", "=", $user->id )->first();

        if($request -> hasFile('image')) {
            if($profile -> image) {
                Storage::disk('public')->delete($profile -> image);
            }
            $profile -> image = $request -> file('image')->store('profile', 'public');
        }
        $profile -> update([
            'phone' => $request -> phone,
            'address' => $request -> address,
            'type' => $request -> type,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $user->load('profile'),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        // Return JSON response with token
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }


}
