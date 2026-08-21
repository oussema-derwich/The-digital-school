<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditService
{
    public function logAction($action, $model, $changes = [], $additional = [])
    {
        $user = Auth::user();
        
        $logData = [
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->id,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : 'system',
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional' => $additional,
            'timestamp' => now()->toIso8601String()
        ];

        // Log dans un canal spécifique pour l'audit
        Log::channel('audit')->info(json_encode($logData));

        // Si c'est une action sensible, notifier les administrateurs
        if (in_array($action, ['delete', 'suspend', 'price_change'])) {
            $this->notifyAdmins($logData);
        }

        return $logData;
    }

    private function notifyAdmins($logData)
    {
        // Ici, vous pouvez implémenter la logique pour notifier les admins
        // par email, Slack, ou autre moyen de communication
    }
}