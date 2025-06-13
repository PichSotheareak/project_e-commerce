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
        return response()->json([
            'data' => ContactUs::all(),
            'count' => ContactUs::count()
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contact_us,email',
            'phone' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:255',
        ]);

        $contactUs = ContactUs::create([
            'first_name' => $request -> first_name,
            'last_name' => $request -> last_name,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'message' => $request -> message,
            'created_at' => now(),
        ]);
        return response()->json([
            'data' => $contactUs,
            'message' => 'Contact Us Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data' => ContactUs::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contactUs = ContactUs::find($id);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contact_us,email,'.$id,
            'phone' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:255',
        ]);

        $contactUs->update([
            'first_name' => $request -> first_name,
            'last_name' => $request -> last_name,
            'email' => $request -> email,
            'phone' => $request -> phone,
            'message' => $request -> message,
            'updated_at' => now(),
        ]);
        return response()->json([
            'data' => $contactUs,
            'message' => 'Contact Us Updated Successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contactUs = ContactUs::find($id);
        if (!$contactUs) {
            return response()->json([
                'message' => 'Contact Us Not Found'
            ]);
        }

        $contactUs->delete();

        return response()->json([
            'message' => 'Contact Us Deleted Successfully'
        ]);
    }
}
