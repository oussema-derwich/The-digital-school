<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Crypto;
use App\Models\Cryptocurrency;
use App\Models\Notification;
use App\Models\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'activeUsers' => User::where('is_active', true)->count(),
                'totalUsers' => User::count(),
                'newUsersThisWeek' => User::where('created_at', '>=', now()->subWeek())->count(),
                'totalTransactions' => Transaction::count(),
                'totalAlerts' => Notification::count(),
                'totalCryptos' => \App\Models\Cryptocurrency::count(),
            ]
        ]);
    }

    /**
     * Get all users with filtering
     */
    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
    }

    /**
     * Suspend a user
     */
    public function suspendUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'suspended']);
        return response()->json(['message' => 'User suspended successfully']);
    }

    /**
     * Activate a user
     */
    public function activateUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'active']);
        return response()->json(['message' => 'User activated successfully']);
    }

    /**
     * Delete a user
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Store a new user (admin creation)
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:client,admin'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true
        ]);

        // Create wallet with initial balance
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 500,
            'public_address' => 'public_' . uniqid(),
            'private_address' => 'private_' . uniqid(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update a user (admin update)
     */
    public function updateUser($userId, Request $request)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'sometimes|string|between:2,100',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'role' => 'sometimes|in:client,admin',
            'is_active' => 'sometimes|boolean'
        ]);

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Get all cryptos
     */
    public function getCryptos()
    {
        return response()->json(Crypto::all());
    }

    /**
     * Add a new crypto
     */
    public function addCrypto(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:cryptos',
            'symbol' => 'required|string|unique:cryptos',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'status' => 'required|in:Actif,Inactif'
        ]);

        $crypto = Crypto::create($validated);
        return response()->json($crypto, 201);
    }

    /**
     * Update a crypto
     */
    public function updateCrypto($cryptoId, Request $request)
    {
        $validated = $request->validate([
            'name' => 'string',
            'symbol' => 'string',
            'price' => 'numeric',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'status' => 'in:Actif,Inactif'
        ]);

        $crypto = Crypto::findOrFail($cryptoId);
        $crypto->update($validated);
        return response()->json($crypto);
    }

    /**
     * Delete a crypto
     */
    public function deleteCrypto($cryptoId)
    {
        Crypto::findOrFail($cryptoId)->delete();
        return response()->json(['message' => 'Crypto deleted successfully']);
    }

    /**
     * Get all transactions
     */
    public function getTransactions(Request $request)
    {
        try {
            $query = Transaction::with(['walletCrypto.wallet.user', 'walletCrypto.cryptocurrency']);

            if ($request->filled('user_id')) {
                $query->whereHas('walletCrypto.wallet', function($q) {
                    $q->where('user_id', request()->input('user_id'));
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->filled('cryptocurrency_id')) {
                $query->whereHas('walletCrypto', function($q) {
                    $q->where('cryptocurrency_id', request()->input('cryptocurrency_id'));
                });
            }

            $page = $request->input('page', 1);
            $per_page = $request->input('per_page', 10);

            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($per_page, ['*'], 'page', $page);

            return response()->json([
                'status' => 'success',
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'last_page' => $transactions->lastPage(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a transaction
     */
    public function cancelTransaction($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $transaction->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Transaction cancelled successfully']);
    }

    /**
     * Get all alerts
     */
    public function getAlerts(Request $request)
    {
        $query = Notification::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    /**
     * Update an alert
     */
    public function updateAlert($alertId, Request $request)
    {
        $validated = $request->validate([
            'threshold' => 'numeric',
            'type' => 'string',
            'status' => 'string'
        ]);

        $alert = Notification::findOrFail($alertId);
        $alert->update($validated);
        return response()->json($alert);
    }

    /**
     * Pause an alert
     */
    public function pauseAlert($alertId)
    {
        $alert = Notification::findOrFail($alertId);
        $alert->update(['is_read' => true]);
        return response()->json(['message' => 'Alert paused successfully']);
    }

    /**
     * Resume an alert
     */
    public function resumeAlert($id)
    {
        try {
            $alert = Notification::findOrFail($id);
            $alert->update(['status' => 'active']);
            return response()->json([
                'status' => 'success',
                'message' => 'Alert resumed successfully',
                'data' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error resuming alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an alert
     */
    public function deleteAlert($alertId)
    {
        Notification::findOrFail($alertId)->delete();
        return response()->json(['message' => 'Alert deleted successfully']);
    }

    /**
     * Save platform settings
     */
    public function savePlatformSettings(Request $request)
    {
        $settings = $request->validate([
            'platformName' => 'string',
            'description' => 'string',
            'supportEmail' => 'email'
        ]);

        // Store in cache or database as needed
        return response()->json(['message' => 'Platform settings saved']);
    }

    /**
     * Save security settings
     */
    public function saveSecuritySettings(Request $request)
    {
        $settings = $request->validate([
            'requireTwoFA' => 'boolean',
            'sessionTimeout' => 'integer',
            'requireStrongPassword' => 'boolean'
        ]);

        return response()->json(['message' => 'Security settings saved']);
    }

    /**
     * Save notification settings
     */
    public function saveNotificationSettings(Request $request)
    {
        $settings = $request->validate([
            'notifyNewUsers' => 'boolean',
            'notifyTransactions' => 'boolean',
            'notifyAlerts' => 'boolean'
        ]);

        return response()->json(['message' => 'Notification settings saved']);
    }

    /**
     * Get settings
     */
    public function getSettings()
    {
        return response()->json([
            'platformName' => 'BitChest',
            'description' => 'Plateforme d\'échange de cryptomonnaies sécurisée',
            'supportEmail' => 'support@bitchest.com',
            'requireTwoFA' => true,
            'sessionTimeout' => 30,
            'requireStrongPassword' => true,
            'notifyNewUsers' => true,
            'notifyTransactions' => true,
            'notifyAlerts' => true
        ]);
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $settings = $request->validate([
            'platformName' => 'nullable|string',
            'description' => 'nullable|string',
            'supportEmail' => 'nullable|email',
            'requireTwoFA' => 'nullable|boolean',
            'sessionTimeout' => 'nullable|integer',
            'requireStrongPassword' => 'nullable|boolean',
            'notifyNewUsers' => 'nullable|boolean',
            'notifyTransactions' => 'nullable|boolean',
            'notifyAlerts' => 'nullable|boolean'
        ]);

        // Store settings (you can use cache or database)
        return response()->json([
            'status' => 'success',
            'message' => 'Settings updated successfully',
            'data' => $settings
        ]);
    }

    /**
     * Get all pending registration requests
     */
    public function getRegistrationRequests(Request $request)
    {
        $query = RegistrationRequest::with('user');

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->where('is_approved', false)->where('is_rejected', false);
            } elseif ($status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($status === 'rejected') {
                $query->where('is_rejected', true);
            }
        }

        $page = $request->input('page', 1);
        $per_page = $request->input('per_page', 10);

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $requests->items(),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
                'last_page' => $requests->lastPage(),
                'from' => $requests->firstItem(),
                'to' => $requests->lastItem(),
            ]
        ]);
    }

    /**
     * Get registration request details by ID
     */
    public function getRegistrationRequestDetails($id)
    {
        try {
            $registrationRequest = RegistrationRequest::with('user')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $registrationRequest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration request not found',
            ], 404);
        }
    }

    /**
     * Update a registration request
     */
    public function updateRegistrationRequest($id, Request $request)
    {
        try {
            $registrationRequest = RegistrationRequest::findOrFail($id);

            // Ne pas modifier si déjà approuvée ou rejetée
            if ($registrationRequest->is_approved || $registrationRequest->is_rejected) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot modify an approved or rejected request'
                ], 400);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|between:2,100',
                'email' => 'sometimes|email|unique:users,email,' . $registrationRequest->user_id,
                'role' => 'sometimes|in:client,trader,analyst,admin',
            ]);

            // Mettre à jour l'utilisateur
            if (isset($validated['name']) || isset($validated['email'])) {
                $registrationRequest->user->update([
                    'name' => $validated['name'] ?? $registrationRequest->user->name,
                    'email' => $validated['email'] ?? $registrationRequest->user->email,
                ]);
            }

            // Mettre à jour la demande
            if (isset($validated['role'])) {
                $registrationRequest->update(['role' => $validated['role']]);
                $registrationRequest->user->update(['role' => $validated['role']]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Registration request updated successfully',
                'data' => $registrationRequest->with('user')->first()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating registration request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a registration request
     */
    public function deleteRegistrationRequest($id)
    {
        try {
            $registrationRequest = RegistrationRequest::findOrFail($id);
            $user = $registrationRequest->user;

            // Supprimer l'utilisateur associé s'il n'est pas approuvé
            if (!$registrationRequest->is_approved) {
                // Vous pouvez aussi juste supprimer la demande
                $registrationRequest->delete();
                if ($user) {
                    $user->delete();
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete an approved registration request'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Registration request deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting registration request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve a registration request
     */
    public function approveRegistrationRequest($requestId)
    {
        try {
            $registrationRequest = RegistrationRequest::findOrFail($requestId);
            
            if ($registrationRequest->is_approved) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La demande a déjà été approuvée'
                ], 400);
            }

            if ($registrationRequest->is_rejected) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La demande a été rejetée'
                ], 400);
            }

            $user = $registrationRequest->user;

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            // Générer un password temporaire fort
            $tempPassword = \Illuminate\Support\Str::random(12);

            // Mettre à jour le password de l'utilisateur
            $user->password = Hash::make($tempPassword);
            $user->email_verified_at = now(); // Valider l'email automatiquement
            $user->is_active = true;
            $user->save();

            // Créer le wallet pour l'utilisateur s'il n'existe pas
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 500 // Solde initial EUR
                ]);
            }

            // Sauvegarder le password temporaire dans la demande
            $registrationRequest->temp_password = $tempPassword;
            $registrationRequest->is_approved = true;
            $registrationRequest->save();

            // Envoyer l'email avec le password temporaire
            $emailSent = false;
            $emailError = null;
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\RegistrationApprovedMail($user, $tempPassword));
                
                // Tracker que l'email a été envoyé avec succès
                $emailSent = true;
                $registrationRequest->approval_email_sent = true;
                $registrationRequest->approval_email_sent_at = now();
                $registrationRequest->approval_email_error = null;
                $registrationRequest->save();
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de l\'email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                
                // Tracker l'erreur d'email
                $emailError = $e->getMessage();
                $registrationRequest->approval_email_sent = false;
                $registrationRequest->approval_email_error = $emailError;
                $registrationRequest->save();
            }

            // Créer une notification
            Notification::create([
                'user_id' => $user->id,
                'message' => 'Votre demande d\'inscription a été approuvée! Vérifiez votre email pour vos identifiants de connexion.',
                'is_read' => false
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $emailSent 
                    ? 'Demande d\'inscription approuvée. Email avec le password envoyé à ' . $user->email
                    : 'Demande d\'inscription approuvée. ⚠️ L\'email n\'a pas pu être envoyé. Erreur: ' . $emailError,
                'warning' => $emailSent ? null : 'email_not_sent',
                'data' => [
                    'id' => $registrationRequest->id,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active
                    ],
                    'is_approved' => true
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'approbation de l\'inscription', [
                'registration_request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'approbation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a registration request
     */
    public function rejectRegistrationRequest($requestId, Request $request)
    {
        $registrationRequest = RegistrationRequest::findOrFail($requestId);
        
        if ($registrationRequest->is_rejected) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request already rejected'
            ], 400);
        }

        $reason = $request->input('reason', 'Your registration request has been rejected.');

        // Mark request as rejected
        $registrationRequest->update(['is_rejected' => true]);

        // Send notification to user
        Notification::create([
            'user_id' => $registrationRequest->user_id,
            'message' => $reason,
            'is_read' => false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration request rejected successfully',
            'data' => $registrationRequest
        ]);
    }

    /**
     * Get recent activities
     */
    public function getActivities(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            
            $transactions = Transaction::with(['walletCrypto.wallet.user', 'walletCrypto.cryptocurrency'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching activities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction chart data
     */
    public function getChartData(Request $request)
    {
        try {
            $period = $request->input('period', 7); // days
            $startDate = now()->subDays($period);

            $data = Transaction::select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_price) as total'),
                'type'
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy('type');

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }
}

