<?php

namespace App\Http\Services;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\Cryptocurrency;
use Pusher\Pusher;

class AlertService
{
    protected $pusher;

    public function __construct()
    {
        $pusherKey = env('PUSHER_APP_KEY');
        $pusherSecret = env('PUSHER_APP_SECRET');
        $pusherId = env('PUSHER_APP_ID');
        $pusherCluster = env('PUSHER_APP_CLUSTER', 'eu');

        if (empty($pusherKey) || empty($pusherSecret) || empty($pusherId)) {
            throw new \Exception("Pusher credentials are missing in the .env file.");
        }

        $this->pusher = new Pusher($pusherKey, $pusherSecret, $pusherId, [
            'cluster' => $pusherCluster,
            'useTLS' => true
        ]);
    }



    public function getAllAlerts()
    {
        return Alert::with('user', 'cryptocurrency')
            ->where('user_id', auth()->id())
            ->get();
    }


    public function createAlert($data)
    {
        return Alert::create($data);
    }

    public function updateAlert($id, $data)
    {
        $alert = Alert::findOrFail($id);
        $alert->update($data);
        return $alert;
    }
    public function toggleAlert($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->status = $alert->status === 'active' ? 'inactive' : 'active';
        $alert->save();
        return $alert;
    }

    public function deleteAlert($id)
    {
        Alert::destroy($id);
    }

    public function checkPriceAlerts()
    {

        $cryptos = Cryptocurrency::with('alerts')->get();

        foreach ($cryptos as $crypto) {
            $currentPrice = $crypto->currentPrice;

            foreach ($crypto->alerts as $alert) {
                if ($alert->status !== 'active') {
                    continue;
                }

                $isTriggered = match ($alert->condition) {
                    'less_than' => $currentPrice < $alert->price_alert,
                    'greater_than' => $currentPrice >= $alert->price_alert,
                    default => false,
                };

                if ($isTriggered) {
                    $this->triggerNotification($alert);
                    $alert->update(['status' => 'triggered']);
                }
            }
        }
    }


    protected function triggerNotification($alert)
    {
        $operatorMessage = match ($alert->condition) {
            'less_than' => "dropped below",
            'greater_than' => "rose above",
            default => "triggered",
        };

        // Formater le price_alert
        $formattedPriceAlert = number_format((float) $alert->price_alert, 3, '.', '');

        $message = "Your alert for {$alert->cryptocurrency->name} has {$operatorMessage} {$formattedPriceAlert}.";

        // Vérifier si une notification avec ce message a déjà été envoyée dans les 5 dernières secondes
        $existingNotification = Notification::where([
            ['user_id', '=', $alert->user_id],
            ['message', '=', $message],
        ])->where('created_at', '>=', now()->subSeconds(5))
            ->first();


        // Créer la notification en base de données
        $notification = Notification::create([
            'user_id' => $alert->user_id,
            'message' => $message,
            'date' => now(),
            'is_read' => false,
        ]);

        // Envoyer la notification via Pusher
        $this->pusher->trigger("notifications.{$notification->user_id}", 'new-notification', [
            'id' => $notification->id,
            'user_id' => $notification->user_id,
            'message' => $notification->message,
            'date' => $notification->date,
            'is_read' => $notification->is_read,
        ]);
    }
}
