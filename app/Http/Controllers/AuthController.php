<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Vérifier l'utilisateur
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Email ou mot de passe incorrect'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => Auth::user()
        ]);
    }
}
