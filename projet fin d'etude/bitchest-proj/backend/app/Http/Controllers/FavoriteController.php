<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Cryptocurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Get user's favorite cryptocurrencies
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $favorites = Favorite::where('user_id', $user->id)
                ->with('cryptocurrency')
                ->get()
                ->map(function ($favorite) {
                    return [
                        'id' => $favorite->cryptocurrency->id,
                        'name' => $favorite->cryptocurrency->name,
                        'symbol' => $favorite->cryptocurrency->symbol,
                        'price' => (float)$favorite->cryptocurrency->current_price,
                        'current_price' => (float)$favorite->cryptocurrency->current_price
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $favorites->values()->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement des favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a cryptocurrency to favorites
     */
    public function store(int $crypto_id): JsonResponse
    {
        $user = Auth::user();
        
        // Verify crypto exists
        $crypto = Cryptocurrency::find($crypto_id);
        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomonnaie non trouvée'
            ], 404);
        }

        // Check if already favorited
        $existing = Favorite::where('user_id', $user->id)
            ->where('cryptocurrency_id', $crypto_id)
            ->exists();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette cryptomonnaie est déjà dans les favoris'
            ], 409);
        }

        // Create favorite
        Favorite::create([
            'user_id' => $user->id,
            'cryptocurrency_id' => $crypto_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cryptomonnaie ajoutée aux favoris'
        ], 201);
    }

    /**
     * Remove a cryptocurrency from favorites
     */
    public function destroy(int $crypto_id): JsonResponse
    {
        $user = Auth::user();
        
        $deleted = Favorite::where('user_id', $user->id)
            ->where('cryptocurrency_id', $crypto_id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Favori non trouvé'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cryptomonnaie supprimée des favoris'
        ]);
    }

    /**
     * Toggle a cryptocurrency from favorites (add if not present, remove if present)
     */
    public function toggle(int $crypto_id): JsonResponse
    {
        $user = Auth::user();
        
        // Verify crypto exists
        $crypto = Cryptocurrency::find($crypto_id);
        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomonnaie non trouvée'
            ], 404);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('cryptocurrency_id', $crypto_id)
            ->first();

        if ($favorite) {
            // Remove if exists
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Cryptomonnaie supprimée des favoris',
                'is_favorite' => false
            ]);
        } else {
            // Add if doesn't exist
            Favorite::create([
                'user_id' => $user->id,
                'cryptocurrency_id' => $crypto_id
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Cryptomonnaie ajoutée aux favoris',
                'is_favorite' => true
            ], 201);
        }
    }
}
