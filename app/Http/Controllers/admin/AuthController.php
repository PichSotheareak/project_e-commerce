<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login')->with('message', 'Logged out successfully');
    }

    public function showLoginForm(){
        return view('admin.login');
    }

    public function index() {
        return view('admin.User.index');
    }

}
