<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CryptoPriceAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert;
    public $crypto;
    public $currentPrice;

    public function __construct($alert, $crypto, $currentPrice)
    {
        $this->alert = $alert;
        $this->crypto = $crypto;
        $this->currentPrice = $currentPrice;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->alert->user_id);
    }

    public function broadcastAs()
    {
        return 'crypto.alert';
    }

    public function broadcastWith()
    {
        return [
            'message' => "Alert: {$this->crypto->name} has reached {$this->currentPrice}",
            'crypto' => $this->crypto,
            'alert' => $this->alert,
            'current_price' => $this->currentPrice
        ];
    }
}