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
    public function index(Request $request)
    {
        $query = Branches::query();
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:branches,phone',
            'email' => 'required|email|unique:branches,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('branches', 'public');
        }

        $branch = Branches::create($validated);
        return response()->json([
            'data' => $branch,
            'message' => 'Branch created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branches::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        return response()->json(['data' => $branch]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branches::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:branches,phone,' .$id,
            'email' => 'required|email|unique:branches,email,' .$id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($branch->logo) {
                Storage::disk('public')->delete($branch->logo);
            }
            $validated['logo'] = $request->file('logo')->store('branches', 'public');
        } else {
            $validated['logo'] = $branch->logo; // Preserve existing logo
        }

        $branch->update($validated);
        return response()->json([
            'data' => $branch,
            'message' => 'Branch updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branches::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $branch->delete();
        return response()->json(['message' => 'Branch soft deleted successfully']);
    }
    public function restore(string $id)
    {
        $branch = Branches::withTrashed()->find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $branch->restore();
        return response()->json([
            'data' => $branch,
            'message' => 'Branch restored successfully'
        ]);
    }

    public function forceDelete(string $id)
    {
        $branch = Branches::withTrashed()->find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        if ($branch->logo) {
            Storage::disk('public')->delete($branch->logo);
        }

        $branch->forceDelete();
        return response()->json(['message' => 'Branch permanently deleted successfully']);
    }
}
