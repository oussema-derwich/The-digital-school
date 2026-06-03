<?php

namespace App\Http\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Récupérer les notifications d'un utilisateur.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserNotifications($userId)
    {
        // Récupérer l'utilisateur
        $user = User::find($userId);

        if (!$user) {
            return []; // Retourner un tableau vide si l'utilisateur n'existe pas
        }

        // Vérifier si l'utilisateur est un administrateur
        if ($user->role === 'admin') {
            // Récupérer tous les utilisateurs avec le rôle 'admin'
            $adminUserIds = User::where('role', 'admin')->pluck('id');

            // Récupérer toutes les notifications des administrateurs
            return Notification::whereIn('user_id', $adminUserIds)->get();
        } else {
            // Récupérer les notifications de l'utilisateur spécifié
            return Notification::where('user_id', $userId)->get();
        }
    }

    /**
     * Marquer une notification comme lue.
     *
     * @param int $notificationId
     * @return Notification|null
     */
    public function markAsRead($notificationId)
    {
        // Trouver la notification par son ID
        $notification = Notification::find($notificationId);


        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        return $notification;
    }
}
