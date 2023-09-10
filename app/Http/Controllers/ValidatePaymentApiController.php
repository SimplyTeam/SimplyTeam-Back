<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidatePaymentApiControllerRequest;
use Illuminate\Http\Request;

class ValidatePaymentApiController extends Controller
{
    public function index(ValidatePaymentApiControllerRequest $request) {
        $user = $request->user();
        $validatedData = $request->validated();

        $user->update($validatedData);

        return response()->json(['message' => 'Subscription has been successful set']);
    }
}
