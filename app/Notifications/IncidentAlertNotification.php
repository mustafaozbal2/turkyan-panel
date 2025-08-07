<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidentAlertNotification extends Notification
{
    use Queueable;

    protected $incident;

    /**
     * Create a new notification instance.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Bu bildirimi veritabanına ve anlık yayın kanalına gönder
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'incident_id' => $this->incident->id,
            'title' => '🚨 ACİL YANGIN ALARMI 🚨',
            'body' => "{$this->incident->location} bölgesinde '{$this->incident->name}' olayı onaylandı. Detaylar için tıklayın.",
            'url' => route('index'), // Bildirime tıklandığında gidilecek adres
        ];
    }
}