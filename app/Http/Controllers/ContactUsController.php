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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contact_us',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $contactUs = ContactUs::create([
            'name' => $request -> name,
            'email' => $request -> email,
            'subject' => $request -> subject,
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contact_us',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $contactUs->update([
            'name' => $request -> name,
            'email' => $request -> email,
            'subject' => $request -> subject,
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
        $contactUs->delete();
        return response()->json([
            'message' => 'Contact Us Deleted Successfully'
        ]);
    }
}
