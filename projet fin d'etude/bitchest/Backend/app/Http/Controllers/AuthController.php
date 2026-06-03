<?php

namespace App\Http\Controllers;

use App\Models\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\AuthService;
use App\Models\Wallet;

class AuthController extends Controller 
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'pending',
        ]);

        RegistrationRequest::create(['user_id' => $user->id]);

        return response()->json(['message' => 'Registration request submitted.'], 201);
    }
   
    protected $authService;

    /**
     * Create a new AuthController instance.
     *
     * @param \App\Http\Services\AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user login and return a token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) 
{
    // Valider la demande
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Appeler la logique d'authentification dans AuthService
    $user = $this->authService->authenticate($request->email, $request->password);

    // Si l'authentification échoue (utilisateur est nul), retourner un message d'erreur
    if (!$user) {
        return response()->json(['message' => 'Please check your email and password'], 401);
    }

   
    // Créer un token Sanctum pour l'utilisateur authentifié
    $token = $user->createToken('authToken')->plainTextToken;

    // Retourner un message de succès avec le token et les informations de l'utilisateur
    return response()->json([
        'message' => 'Login successful.',
        'token' => $token,
        'user' => $user,
       
    ]);
}

    public function logout(Request $request)
    {
        
        $user = Auth::user();
        // Supprime le token actuel de l'utilisateur
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'User successfully logged out']);
    }
}
