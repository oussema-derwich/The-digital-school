<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\RegistrationRequest;
use App\Mail\RegistrationApprovedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationRequestController extends Controller
{
    /**
     * Créer une demande d'inscription (email + nom seulement)
     * Le password sera généré et envoyé après approbation admin
     */
    public function createRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'role' => 'nullable|string|in:client,trader,analyst,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Utiliser le rôle fourni ou 'client' par défaut
        $role = $request->role ?? 'client';

        // Créer l'utilisateur avec un password placeholder (sera remplacé après approbation)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // Password temporaire
            'role' => $role,
            'is_active' => false
        ]);

        // Créer la demande d'inscription
        RegistrationRequest::create([
            'user_id' => $user->id,
            'email' => $request->email,
            'role' => $role,
            'is_approved' => false,
            'is_rejected' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Demande d\'inscription créée. En attente d\'approbation admin.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * Admin : Récupérer toutes les demandes d'inscription
     */
    public function getAllRequests(): JsonResponse
    {
        $this->authorizeAdmin();

        $requests = RegistrationRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }

    /**
     * Admin : Approuver une demande d'inscription
     * Génère un password temporaire et envoie l'email de confirmation
     */
    public function approveRequest($id): JsonResponse
    {
        $this->authorizeAdmin();

        $registrationRequest = RegistrationRequest::findOrFail($id);
        $user = $registrationRequest->user;

        // Générer un password temporaire fort
        $tempPassword = Str::random(12);

        try {
            // Mettre à jour le password de l'utilisateur
            $user->password = Hash::make($tempPassword);
            $user->email_verified_at = now(); // Valider l'email automatiquement
            $user->is_active = true;
            $user->save();

            // Créer son wallet automatiquement
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 500, // Solde initial
                    'public_address' => 'public_' . uniqid(),
                    'private_address' => 'private_' . uniqid(),
                ]
            );

            // Sauvegarder le password temporaire et approve la demande
            $registrationRequest->temp_password = $tempPassword;
            $registrationRequest->is_approved = true;
            $registrationRequest->save();

            // Envoyer l'email avec le password temporaire
            try {
                Mail::to($user->email)->send(new RegistrationApprovedMail($user, $tempPassword));
                
                // Tracker que l'email a été envoyé avec succès
                $registrationRequest->approval_email_sent = true;
                $registrationRequest->approval_email_sent_at = now();
                $registrationRequest->approval_email_error = null;
                $registrationRequest->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Demande d\'inscription approuvée. Email envoyé avec le password temporaire.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active,
                    ]
                ]);
            } catch (\Exception $emailError) {
                // Si l'email échoue, tracker l'erreur mais ne pas échouer la demande
                \Log::error('Erreur lors de l\'envoi de l\'email d\'approbation', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $emailError->getMessage()
                ]);

                // Tracker l'erreur d'email
                $registrationRequest->approval_email_sent = false;
                $registrationRequest->approval_email_error = $emailError->getMessage();
                $registrationRequest->save();

                // Retourner un succès partial - l'utilisateur est approuvé mais l'email n'a pas pu être envoyé
                return response()->json([
                    'status' => 'success',
                    'message' => 'Demande d\'inscription approuvée. ⚠️ L\'email n\'a pas pu être envoyé (erreur SMTP). Vous pouvez renvoyer l\'email manuellement.',
                    'warning' => 'email_not_sent',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'approbation de l\'inscription', [
                'registration_request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'approbation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin : Renvoyer l'email d'approbation à un utilisateur approuvé
     * Utile si l'envoi initial a échoué
     */
    public function resendApprovalEmail($id): JsonResponse
    {
        $this->authorizeAdmin();

        $registrationRequest = RegistrationRequest::findOrFail($id);
        $user = $registrationRequest->user;

        // Vérifier que la demande est approuvée
        if (!$registrationRequest->is_approved || !$registrationRequest->temp_password) {
            return response()->json([
                'status' => 'error',
                'message' => 'La demande d\'inscription n\'a pas été approuvée ou le password temporaire est manquant.',
            ], 400);
        }

        try {
            // Renvoyer l'email avec le password temporaire
            Mail::to($user->email)->send(new RegistrationApprovedMail($user, $registrationRequest->temp_password));
            
            // Tracker que l'email a été renvoyé avec succès
            $registrationRequest->approval_email_sent = true;
            $registrationRequest->approval_email_sent_at = now();
            $registrationRequest->approval_email_error = null;
            $registrationRequest->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Email d\'approbation renvoyé avec succès à ' . $user->email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du renvoi de l\'email d\'approbation', [
                'registration_request_id' => $id,
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            // Tracker l'erreur
            $registrationRequest->approval_email_sent = false;
            $registrationRequest->approval_email_error = $e->getMessage();
            $registrationRequest->save();

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du renvoi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin : Rejeter une demande d'inscription
     */
    public function rejectRequest(Request $request, $id): JsonResponse
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $registrationRequest = RegistrationRequest::findOrFail($id);
        $user = $registrationRequest->user;

        // Marquer comme rejetée
        $registrationRequest->is_rejected = true;
        $registrationRequest->rejection_reason = $request->rejection_reason;
        $registrationRequest->save();

        // Supprimer l'utilisateur (optionnel)
        // $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Demande d\'inscription rejetée',
        ]);
    }

    /**
     * Admin : Vérifier l'état d'une demande d'inscription
     */
    public function getRequestStatus($id): JsonResponse
    {
        $registrationRequest = RegistrationRequest::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $registrationRequest
        ]);
    }

    /**
     * Vérifier que l'utilisateur est admin
     */
    private function authorizeAdmin(): void
    {
        $user = auth('sanctum')->user();
        
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Accès refusé. Vous devez être administrateur.');
        }
    }
}
