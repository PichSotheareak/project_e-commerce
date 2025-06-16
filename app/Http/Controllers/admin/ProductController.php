<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return view('admin.Product.index');
    }


}
