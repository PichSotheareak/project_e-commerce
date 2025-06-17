<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = User::with('profile');
        if ($request->has('with_deleted') && $request->with_deleted) {
            $query->withTrashed()->with(['profile' => function ($q) {
                $q->withTrashed();
            }]);
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
            'gender' => 'nullable|string|in:Male,Female',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'nullable|string|in:Enable,Disable',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        return DB::transaction(function () use ($request, $validated) {
            $userData = [
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'],
            ];

            $user = User::create($userData);

            $profileData = [
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'type' => $validated['type'] ?? null,
            ];

            if ($request->hasFile('image')) {
                $profileData['image'] = $request->file('image')->store('users', 'public');
            }

            $user->profile()->create($profileData);

            return response()->json([
                'data' => User::with('profile')->find($user->id),
                'message' => 'User created successfully'
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('profile')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'gender' => 'required|in:Male,Female',
            'status' => 'required|in:Enable,Disable',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'remove_image' => 'nullable|string|in:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'status' => $request->status,
        ];
        if ($request->password) {
            $userData['password'] = bcrypt($request->password);
        }
        $user->update($userData);

        // Update profile
        $profileData = [
            'phone' => $request->phone,
            'address' => $request->address,
            'type' => $request->type,
        ];

        // Handle image
        if ($request->remove_image === '1' && $user->profile->image) {
            Storage::delete('public/' . $user->profile->image); // Delete file
            $profileData['image'] = null; // Clear image path
        } elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->profile->image) {
                Storage::delete('public/' . $user->profile->image);
            }
            // Store new image
            $path = $request->file('image')->store('profiles', 'public');
            $profileData['image'] = $path;
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load('profile')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return DB::transaction(function () use ($user) {
            if ($user->profile) {
                $user->profile->delete();
            }
            $user->delete();
            return response()->json(['message' => 'User soft deleted successfully']);
        });
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return DB::transaction(function () use ($user) {
            $user->restore();
            if ($user->profile()->withTrashed()->exists()) {
                $user->profile()->restore();
            }
            return response()->json([
                'data' => User::with('profile')->find($user->id),
                'message' => 'User restored successfully'
            ]);
        });
    }

    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return DB::transaction(function () use ($user) {
            if ($user->profile()->withTrashed()->exists()) {
                if ($user->profile()->withTrashed()->first()->image) {
                    Storage::disk('public')->delete($user->profile()->withTrashed()->first()->image);
                }
                $user->profile()->forceDelete();
            }
            $user->forceDelete();
            return response()->json(['message' => 'User permanently deleted successfully']);
        });
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password does not match'], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User login successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

}
