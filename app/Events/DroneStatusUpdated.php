<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DroneStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Incident $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function broadcastOn(): array
    {
        // Bu yayını, sadece ilgili olaya ait özel bir kanalda yap.
        return [
            new PrivateChannel('incident.' . $this->incident->id),
        ];
    }
}
