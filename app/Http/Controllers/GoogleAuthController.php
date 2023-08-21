<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * @OA\Tag(
 *     name="Google Authentication",
 *     description="API Endpoints for Google OAuth2.0 Authentication"
 * )
 */
class GoogleAuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/auth/google/redirect",
     *     tags={"Google Authentication"},
     *     summary="Redirect to Google for authentication",
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to Google OAuth2.0 authorization page"
     *     )
     * )
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @OA\Get(
     *     path="/auth/google/callback",
     *     tags={"Google Authentication"},
     *     summary="Handle the callback from Google",
     *     @OA\Response(
     *         response=302,
     *         description="Redirect back to the web application with an access token or error message"
     *     )
     * )
     */
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
