<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegistrationRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        try {
            $validatedData = $request->validated();

            $validatedData['password'] = bcrypt($request->password);

            $user = User::create($validatedData);

            $accessToken = $user->createToken('API Token')->accessToken;

            return response(['user' => $user, 'access_token' => $accessToken], 201);
        }catch (\Exception $e){
            return response(['error' => $e->getMessage()]);
        }
    }

    public function login(UserLoginRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $loginData = $request->validated();

        $user = User::where('email', $loginData['email'])->first();

        if (!$user || !Hash::check($loginData['password'], $user->password)) {
            return response(['errors' => ['message' => 'Informations d\'authentification invalides']], 422);
        }

        $accessToken = $user->createToken('API Token')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken], 201);
    }

    public function me(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = $request->user();

        return response(['user' => $user], 200);
    }

    public function logout(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $request->user()->token()->revoke();
        return response(['message' => 'Déconnexion réussie'], 200);
    }
}
