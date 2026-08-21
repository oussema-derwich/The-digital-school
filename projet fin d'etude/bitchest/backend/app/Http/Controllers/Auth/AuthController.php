<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user (deprecated - use registration request instead)
     */
    public function register(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'L\'inscription directe est désactivée. Utilisez la demande d\'inscription.',
        ], 403);
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            \Log::info('Tentative de connexion', ['email' => $request->email]);
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get user directly with simple query
            $userRecord = \DB::table('users')->where('email', $request->email)->first();

            if (!$userRecord || !Hash::check($request->password, $userRecord->password)) {
                if ($userRecord) {
                    \DB::table('users')->where('id', $userRecord->id)->increment('login_attempts');

                    if ($userRecord->login_attempts >= 3) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Compte temporairement bloqué. Réessayez plus tard.'
                        ], 401);
                    }
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Identifiants non valides'
                ], 401);
            }

            if (!$userRecord->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Compte désactivé'
                ], 401);
            }

            // Reset login attempts
            \DB::table('users')->where('id', $userRecord->id)->update([
                'login_attempts' => 0,
                'last_login_attempt' => Carbon::now()
            ]);

            // Now get the Eloquent model for token generation
            $user = User::find($userRecord->id);
            
            // Créer token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // Préparer les données utilisateur avec avatar URL complète
            $userData = [
                'id' => $userRecord->id,
                'name' => $userRecord->name,
                'email' => $userRecord->email,
                'role' => $userRecord->role,
                'is_active' => (bool)$userRecord->is_active,
            ];
            
            if ($userRecord->avatar) {
                $userData['avatar'] = str_starts_with($userRecord->avatar, 'http') 
                    ? $userRecord->avatar 
                    : asset('storage/' . $userRecord->avatar);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Connexion réussie',
                'user' => $userData,
                'token' => $token,
                'token_type' => 'Bearer',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $userData
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur login:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur: vérifiez que MySQL est en cours d\'exécution',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Log the user out (Invalidate the token) - Optimized for speed
     */
    public function logout(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Utilisateur déjà déconnecté'
                ], 200);
            }

            // Delete token asynchronously for faster response
            $token = $user->currentAccessToken();
            if ($token) {
                // Queue the deletion for async processing or delete directly
                $token->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Déconnexion réussie',
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur logout:', ['error' => $e->getMessage()]);
            // Return success anyway for logout to work on frontend
            return response()->json([
                'status' => 'success',
                'message' => 'Déconnexion traitée',
                'timestamp' => now()->toIso8601String()
            ], 200);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function profile(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $userData = $user->toArray();
        
        // Convert avatar path to full URL
        if ($user->avatar) {
            $userData['avatar'] = str_starts_with($user->avatar, 'http') 
                ? $user->avatar 
                : asset('storage/' . $user->avatar);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $userData
        ]);
    }

    /**
     * Update the authenticated User profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|between:2,100',
            'email' => 'sometimes|string|email|max:100|unique:users,email,' . auth('sanctum')->id(),
            'password' => 'sometimes|string|min:8|confirmed',
            'old_password' => 'required_with:password|string',
            'avatar' => 'sometimes|file|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('sanctum')->user();

        // Vérifier l'ancien mot de passe si un nouveau est fourni
        if ($request->has('password')) {
            if (!$request->filled('old_password')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ancien mot de passe requis'
                ], 422);
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ancien mot de passe incorrect'
                ], 401);
            }

            $user->password = Hash::make($request->password);
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        $userData = $user->toArray();
        if ($user->avatar) {
            $userData['avatar'] = str_starts_with($user->avatar, 'http') 
                ? $user->avatar 
                : asset('storage/' . $user->avatar);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profil mis à jour avec succès',
            'user' => $userData
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|file|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth('sanctum')->user();
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar mis à jour avec succès',
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null
        ]);
    }

    public function deleteAvatar(): JsonResponse
    {
        $user = auth('sanctum')->user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar supprimé avec succès'
        ]);
    }
}
