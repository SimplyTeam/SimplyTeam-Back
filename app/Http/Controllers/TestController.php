<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    function index(Request $request) {
        return response()->json(["response" => "ok"]);
    }
}
