<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CekResiController extends Controller
{
    public function index(Request $request)
    {
        return view('cek-resi'); 
    }
}