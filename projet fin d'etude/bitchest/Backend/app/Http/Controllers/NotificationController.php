<?php

namespace App\Http\Controllers;

use App\Http\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = $this->notificationService->getUserNotifications($userId);
        return response()->json($notifications, 200);
    }

    /**
     * Marquer une notification comme lue.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        // Utiliser le service pour marquer la notification comme lue
        $notification = $this->notificationService->markAsRead($id);

        // Si la notification n'existe pas, retourner une erreur 404
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Retourner la notification mise à jour
        return response()->json($notification);
    }
}
