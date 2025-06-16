<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        return response()->json([
//            'data' => Staff::with(['branches'])->get(),
//        ]);
        return Staff::with('branches')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|unique:staff,phone',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'current_address' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'branches_id' => 'required|exists:branches,id',
        ]);

        if ($request->hasFile('profile')) {
            $profile = $request->file('profile')->store('staff', 'public');
        }else{
            $profile = null;
        }

        $staff = Staff::create([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'profile' => $profile,
            'current_address' => $request -> current_address,
            'position' => $request -> position,
            'salary' => $request -> salary,
            'branches_id' => $request -> branches_id,
        ]);
        return response()->json([
            'data' => $staff,
            'message' => 'Staff added successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json(['message' => 'staff not found'], 404);
        }

        $staff->load('branches');
        return response()->json($staff);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $staff = Staff::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:6',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'required|string|unique:staff,phone,' . $id,
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'current_address' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'branches_id' => 'required|exists:branches,id',
        ]);

        $profilePath = $staff->profile;

        if ($request->input('remove_profile') === '1') {
            if ($staff->profile) {
                Storage::disk('public')->delete($staff->profile);
            }
            $profilePath = null;
        } elseif ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')->store('staff', 'public');
        }

        $staff->update([
            'name' => $request -> name,
            'gender' => $request -> gender,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'profile' => $profilePath,
            'current_address' => $request -> current_address,
            'position' => $request -> position,
            'salary' => $request -> salary,
            'branches_id' => $request -> branches_id,
        ]);
        return response()->json([
            'data' => $staff,
            'message' => 'Staff updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json(['message' => 'staff not found'], 404);
        }
        if ($staff->profile) {
            Storage::disk('public')->delete($staff->profile);
        }
        $staff->delete();

        return response()->json([
            'message' => 'Staff deleted successfully',
        ]);
    }


}
