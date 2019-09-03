<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloWorld extends Controller
{
    public function index()
    {
        return response()->json(['title' => 'Hello World']);
    }
}
