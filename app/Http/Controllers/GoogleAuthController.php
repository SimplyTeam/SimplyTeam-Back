<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

            $user = $existingUser;
        } else {

            $newUser = User::create(
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => bcrypt(Str::random(16))
                ]
            );

            // Connectez le nouvel utilisateur
            Auth::login($newUser, true);

            $user = $newUser;

        }

        // Créez un token d'accès pour le nouvel utilisateur en utilisant Laravel Passport
        $token = $user->createToken('API Token')->accessToken;

        return redirect(getenv('WEBAPP_REDIRECT_URI') . "?access_token=" . urlencode("$token"));
    }
}
