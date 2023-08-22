<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function index(Request $request) {
        $nbUser = User::query()->count();
        return response()->json([
            "response" => "ok",
            "nbUser" => $nbUser
        ]);
    }
}
