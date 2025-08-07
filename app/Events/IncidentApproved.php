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

class IncidentApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Onaylanan olayın kendisi.
     * Bu özellik 'public' olmalıdır ki yayınlandığında otomatik olarak paylaşılsın.
     */
    public Incident $incident;

    /**
     * Create a new event instance.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Bu event'i 'alarms' adında özel bir kanalda yayınla.
        // Sadece yetkili kullanıcılar bu kanalı dinleyebilecek.
        return [
            new PrivateChannel('alarms'),
        ];
    }
}