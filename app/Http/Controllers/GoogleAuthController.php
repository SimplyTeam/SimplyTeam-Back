<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();

        // Vérifiez si l'utilisateur existe déjà dans votre base de données
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Connectez l'utilisateur existant
            Auth::login($existingUser, true);
        } else {

            $newUser = User::create(
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => bcrypt(str_random(16))
                ]
            );

            // Créez un token d'accès pour le nouvel utilisateur en utilisant Laravel Passport
            $token = $newUser->createToken('Token Name')->accessToken;

            // Connectez le nouvel utilisateur
            Auth::login($newUser, true);
        }

        return redirect()->route('home')->with(['access_token' => $token]); // Redirigez l'utilisateur vers la page d'accueil avec le token d'accès
    }
}
