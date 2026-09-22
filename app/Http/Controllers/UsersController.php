<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController
{
    public function register(UserRequest $request) {
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();

        $token = $user->createToken('token-name', ['user'])->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        // Validation des données, email et mot de passe sont obligatoires et l'email doit être un email
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Authentification du user, `attempt` permet de vérifier les identifiants du user
        // Si ils sont incorrects, une erreur 401 est retournée (Unauthorized)
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Récupération du user authentifié (avec le auth()->attempt, le user est authentifié)
        $user = auth()->user();

        // Création d'un token pour le user (sanctum, le token sera stocké en base de données et sera utilisé pour authentifier le user dans les prochaines requêtes)
        // via un Bearer Token
        $token = $user->createToken('token-name', ['user'])->plainTextToken;

        // Retourne une réponse JSON avec le user et le token
        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out',
        ]);
    }
}
