<?php

namespace App\Http\Services;

use App\Models\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Models\Notification;
use App\Models\Wallet;

class RegistrationRequestService
{

    public function checkExistingRequests(string $email)
    {

        if (RegistrationRequest::where('email', $email)->exists()) {
            return [
                'message' => 'You have already submitted a registration request. Please wait for approval.',
                'status' => 'existing_request'
            ];
        }

        if (User::where('email', $email)->exists()) {
            return [
                'message' => 'Email already registered. Redirecting to Sign In.',
                'redirect' => '/login',
                'status' => 'user_exists'
            ];
        }

        return [
            'message' => 'Your request has been received. You will be notified soon.',
            'status' => 'new_request'
        ];
    }


    public function createRequest(string $email)
    {
        // Créer la demande d'inscription
        $registrationRequest = RegistrationRequest::create(['email' => $email]);

        // Récupérer l'ID de l'administrateur
        $admin = User::where('role', 'admin')->first();

        // Créer une notification pour l'administrateur
        Notification::create([
            'user_id' => $admin->id,
            'message' => "New registration request received from : $email",
            'date' => now(),
            'is_read' => false,
        ]);

        return $registrationRequest;
    }

    public function getAllRequests()
    {
        return RegistrationRequest::all();
    }

    public function approveRequest($requestId)
    {
        $registrationRequest = RegistrationRequest::findOrFail($requestId);

        // Générer un mot de passe aléatoire
        $password = Str::random(10);

        // Créer l'utilisateur avec l'email fourni
        $user = User::create([
            'email' => $registrationRequest->email,
            'password' => Hash::make($password),
            'role' => 'client', // Définir un rôle par défaut
        ]);

        // Créer un wallet pour l'utilisateur

        Wallet::create([
            'idUser' => $user->id,
            'balance' => 0, // Solde initial
            'publicAdress' => '0x' . Str::random(64), // Adresse publique aléatoire
            'privateAdress' => '0x' . hash('sha256', Str::random(64)), // Adresse privée aléatoire (hashée pour plus de sécurité)
        ]);


        // Lien d'accès à l'application
        $loginUrl = url('http://localhost:5173/login');

        // Envoyer un e-mail avec le mot de passe et le lien d'accès
        Mail::to($user->email)->send(new UserApprovedMail($user->email, $password, $loginUrl));

        // Marquer la demande comme approuvée
        $registrationRequest->is_approved = true;
        $registrationRequest->user_id = $user->id;
        $registrationRequest->save();

        return $user;
    }


    public function rejectRequest($requestId, $rejectionMessage = 'Your registration request has been rejected.')
    {
        $registrationRequest = RegistrationRequest::findOrFail($requestId);

        // Marquer la demande comme rejetée
        $registrationRequest->is_rejected = true;
        $registrationRequest->save();

        // Envoyer un e-mail de rejet
        Mail::to($registrationRequest->email)->send(new UserRejectedMail($registrationRequest->email, $rejectionMessage));

        return $registrationRequest;
    }
}
