<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json([
                'data' => ContactUs::all(),
                'count' => ContactUs::count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve contacts'], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:contact_us,email',
                'phone' => 'nullable|string|max:255',
                'message' => 'nullable|string|max:255',
            ]);

            $contactUs = ContactUs::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
            ]);

            return response()->json([
                'data' => $contactUs,
                'message' => 'Contact Us Created Successfully'
            ], 201); // 201 Created status
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create contact'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contactUs = ContactUs::find($id);
        if (!$contactUs) {
            return response()->json(['message' => 'Contact Us Not Found'], 404);
        }
        return response()->json(['data' => $contactUs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $contactUs = ContactUs::find($id);
            if (!$contactUs) {
                return response()->json(['message' => 'Contact Us Not Found'], 404);
            }

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:contact_us,email,' . $id,
                'phone' => 'nullable|string|max:255',
                'message' => 'nullable|string|max:255',
            ]);

            $contactUs->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
            ]);

            return response()->json([
                'data' => $contactUs,
                'message' => 'Contact Us Updated Successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update contact'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $contactUs = ContactUs::find($id);
            if (!$contactUs) {
                return response()->json(['message' => 'Contact Us Not Found'], 404);
            }

            $contactUs->delete();

            return response()->json(['message' => 'Contact Us Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete contact'], 500);
        }
    }
}
