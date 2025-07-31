<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HaritaController extends Controller
{
    public function index()
    {
        return view('harita');
    }
}