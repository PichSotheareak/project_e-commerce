<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branches;
use Illuminate\Support\Facades\Storage;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Branches::all(),
            'total' => Branches::count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:branches,phone',
            'email' => 'required|email|unique:branches,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('branches', 'public');
        }

        $branches = Branches::create([
            'name' => $request -> name,
            'address' => $request -> address,
            'phone' => $request -> phone,
            'email' => $request -> email,
            'logo' =>  $logoPath,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $branches,
            'message' => 'Branch created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => Branches::find($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branches = Branches::find($id);
        $request->validate([
            'name' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:branches,phone,' .$id,
            'email' => 'required|email|unique:branches,email,' .$id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($branches->logo) {
                Storage::disk('public')->delete($branches->logo);
            }

            $logoPath = $request->file('logo')->store('branches', 'public');
            $branches->logo = $logoPath;
        }

        $branches->update([
            'name' => $request -> name,
            'address' => $request -> address,
            'phone' => $request -> phone,
            'email' => $request -> email,
            'logo' =>  $logoPath,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $branches,
            'message' => 'Branch updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branches = Branches::find($id);
        $branches->delete();
        return response()->json([
            'message' => 'Branch deleted successfully',
        ]);
    }
}
