<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            'password' => 'required|string|confirmed|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $user = User::create([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'password' => Hash::make($request -> password),
            'created_at' => now(),
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('profile', 'public');
        }

        Profile::create([
            'user_id' => $user -> id,
            'phone' => $request -> phone,
            'address' => $request -> address,
            'type' => $request -> type,
            'image' => $image,
            'created_at' => now(),
        ]);

        return response()->json([
            'data' => $user->load('profile')->get(),
            'message' => 'User created successfully'
        ]);
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
            'email' => 'required|email|unique:users,email' .$id,
            'password' => 'required|string|confirmed|min:8',
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
            'data' => $user->load('profile')->get(),
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
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'user' => JWTAuth::user()->load('profile')
        ]);
    }
}
