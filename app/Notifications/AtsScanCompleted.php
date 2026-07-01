<?php

namespace App\Notifications;

use App\Models\AtsScan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AtsScanCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $scan;

    /**
     * Create a new notification instance.
     */
    public function __construct(AtsScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'scan_id' => $this->scan->id,
            'job_title' => $this->scan->job_title ?: 'Untitled Job',
            'job_company' => $this->scan->job_company,
            'score' => $this->scan->score,
            'message' => 'Your ATS scan for ' . ($this->scan->job_title ?: 'Untitled Job') . ' scored ' . $this->scan->score . ' pts.',
        ];
    }
}
