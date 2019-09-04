<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\HelloWorldService;

class HelloWorld extends Controller
{
    /**
     * @param HelloWorldService $service
     */
    public function __construct(HelloWorldService $service){
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->hello());
    }
}
