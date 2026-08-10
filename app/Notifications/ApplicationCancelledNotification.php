<?php

namespace App\Notifications;

use App\Enums\NotificationPreference;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = $notifiable->notification_preference ?? NotificationPreference::ON;

        return match ($preference) {
            NotificationPreference::ON => ['mail', 'database'],
            NotificationPreference::SILENT => ['database'],
            NotificationPreference::OFF => [],
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Application Cancelled - #'.$this->application->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('An application has been cancelled by an agent.')
            ->line('**Application ID:** #'.$this->application->id)
            ->line('**Service:** '.$this->application->service->name)
            ->line('**Amount:** ₹'.number_format((float) $this->application->amount, 2))
            ->line('**Agent:** '.($this->application->agent ? $this->application->agent->name : 'N/A'))
            ->action('View Application', route('admin.applications.show', $this->application))
            ->line('Please note that you may need to process a manual refund if payment was already collected.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'message' => 'Application #'.$this->application->id.' was cancelled by agent.',
            'amount' => $this->application->amount,
            'agent_name' => $this->application->agent ? $this->application->agent->name : null,
            'url' => route('admin.applications.show', $this->application),
        ];
    }
}
