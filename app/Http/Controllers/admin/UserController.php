<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',// Add remember field (checkbox)
        ]);

        $credentials = $request->only('email', 'password');;  // Check if remember is checked

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        return redirect()->route('dashboard')->with('message', 'Login successful');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login')->with('message', 'Logged out successfully');
    }

    public function showLoginForm(){
        return view('admin.login');
    }

}
