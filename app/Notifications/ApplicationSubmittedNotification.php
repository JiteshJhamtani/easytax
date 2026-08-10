<?php

namespace App\Notifications;

use App\Enums\NotificationPreference;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('New Application Submitted - #'.$this->application->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A new application has been successfully submitted and payment was received.')
            ->line('**Application ID:** #'.$this->application->id)
            ->line('**Service:** '.$this->application->service->name)
            ->line('**Amount:** ₹'.number_format((float) $this->application->amount, 2))
            ->line('**Agent:** '.($this->application->agent ? $this->application->agent->name : 'N/A'))
            ->action('View Application', route('admin.applications.show', $this->application))
            ->line('Thank you for using our application!');
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
            'message' => 'New application submitted for '.$this->application->service->name,
            'amount' => $this->application->amount,
            'agent_name' => $this->application->agent ? $this->application->agent->name : null,
            'url' => route('admin.applications.show', $this->application),
        ];
    }
}
