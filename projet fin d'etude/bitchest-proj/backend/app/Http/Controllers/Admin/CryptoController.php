<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CryptoController extends Controller
{
    /**
     * Afficher toutes les cryptomonnaies
     */
    public function index(): JsonResponse
    {
        try {
            $cryptos = Cryptocurrency::all()->map(function ($crypto) {
                return [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'price' => (float)$crypto->current_price,
                    'variation' => (float)($crypto->price_change_24h ?? 0),
                    'status' => $crypto->is_active ? 'Actif' : 'Inactif',
                    'logo' => $crypto->logo_url ?? '/assets/default.png',
                    'description' => $crypto->description ?? '',
                    'created_at' => $crypto->created_at,
                    'updated_at' => $crypto->updated_at
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $cryptos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des cryptomonnaies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une cryptomonnaie
     */
    public function show($id): JsonResponse
    {
        try {
            $crypto = Cryptocurrency::find($id);

            if (!$crypto) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cryptomonnaie non trouvée'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'price' => (float)$crypto->current_price,
                    'variation' => (float)($crypto->price_change_24h ?? 0),
                    'status' => $crypto->is_active ? 'Actif' : 'Inactif',
                    'logo' => $crypto->logo_url ?? '/assets/default.png',
                    'description' => $crypto->description ?? '',
                    'created_at' => $crypto->created_at,
                    'updated_at' => $crypto->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération de la cryptomonnaie: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une cryptomonnaie
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:cryptocurrencies',
                'symbol' => 'required|string|unique:cryptocurrencies',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'status' => 'nullable|in:Actif,Inactif'
            ]);

            $crypto = Cryptocurrency::create([
                'name' => $validated['name'],
                'symbol' => $validated['symbol'],
                'current_price' => $validated['price'],
                'description' => $validated['description'] ?? '',
                'logo_url' => $validated['logo'] ?? '/assets/default.png',
                'is_active' => ($validated['status'] ?? 'Actif') === 'Actif'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cryptomonnaie créée avec succès',
                'data' => [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'price' => (float)$crypto->current_price,
                    'variation' => 0,
                    'status' => $crypto->is_active ? 'Actif' : 'Inactif',
                    'logo' => $crypto->logo_url,
                    'description' => $crypto->description
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de la cryptomonnaie: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une cryptomonnaie
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $crypto = Cryptocurrency::find($id);

            if (!$crypto) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cryptomonnaie non trouvée'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'nullable|string|unique:cryptocurrencies,name,' . $id,
                'symbol' => 'nullable|string|unique:cryptocurrencies,symbol,' . $id,
                'price' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'status' => 'nullable|in:Actif,Inactif'
            ]);

            $crypto->update([
                'name' => $validated['name'] ?? $crypto->name,
                'symbol' => $validated['symbol'] ?? $crypto->symbol,
                'current_price' => $validated['price'] ?? $crypto->current_price,
                'description' => $validated['description'] ?? $crypto->description,
                'logo_url' => $validated['logo'] ?? $crypto->logo_url,
                'is_active' => isset($validated['status']) ? ($validated['status'] === 'Actif') : $crypto->is_active
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cryptomonnaie mise à jour avec succès',
                'data' => [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'price' => (float)$crypto->current_price,
                    'variation' => (float)($crypto->price_change_24h ?? 0),
                    'status' => $crypto->is_active ? 'Actif' : 'Inactif',
                    'logo' => $crypto->logo_url,
                    'description' => $crypto->description
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation échouée',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de la cryptomonnaie: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une cryptomonnaie
     */
    public function destroy($id): JsonResponse
    {
        try {
            $crypto = Cryptocurrency::find($id);

            if (!$crypto) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cryptomonnaie non trouvée'
                ], 404);
            }

            $cryptoName = $crypto->name;
            $crypto->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Cryptomonnaie '{$cryptoName}' supprimée avec succès"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression de la cryptomonnaie: ' . $e->getMessage()
            ], 500);
        }
    }
}
